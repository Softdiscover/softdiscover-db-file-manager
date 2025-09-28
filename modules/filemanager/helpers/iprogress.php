<?php

class iProgress
{
    private $task_name = 'isense';
    private $max_value = 100;
    private $current_value = 0;
    private $messages = array();
    private $last_message = '';
    private $message_history_count = 20;
    private $session_status = 'opened';
    private $abortCalled = false;
    private $progress_file = '';
    private $state = array();
    private $fp = null;
    private $data = array();

    /**
     * Build a safe, non-web-accessible directory for progress files.
     * Prefers system temp, falls back to WP_CONTENT_DIR/softdiscover/backups/.runtime.
     */
    private static function getProgressDir()
    {
        $candidates = array();

        // 1) System temp is usually outside webroot
        $tmp = function_exists('sys_get_temp_dir') ? sys_get_temp_dir() : null;
        if (!empty($tmp)) {
            $candidates[] = rtrim($tmp, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'softdiscover-backups-runtime';
        }

        // 2) WP content runtime (we'll harden with .htaccess/web.config/index.html)
        if (defined('WP_CONTENT_DIR')) {
            $candidates[] = rtrim(WP_CONTENT_DIR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'softdiscover' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR . '.runtime';
        }

        // 3) As a last resort, next to this file BUT inside a hidden sibling folder (still hardened)
        $candidates[] = dirname(__FILE__) . DIRECTORY_SEPARATOR . '.runtime';

        foreach ($candidates as $dir) {
            if (!is_dir($dir)) {
                // Only use WP helper when available, otherwise mkdir recursively.
                if (function_exists('wp_mkdir_p')) {
                    @wp_mkdir_p($dir);
                } else {
                    @mkdir($dir, 0755, true);
                }
            }
            if (is_dir($dir) && is_writable($dir)) {
                self::writeHardeningFiles($dir);
                return $dir;
            }
        }

        // Fallback: current directory (will still be hardened). Not ideal, but prevents failure.
        $fallback = dirname(__FILE__) . DIRECTORY_SEPARATOR . '.runtime';
        if (!is_dir($fallback)) {
            @mkdir($fallback, 0755, true);
        }
        self::writeHardeningFiles($fallback);
        return $fallback;
    }

    /**
     * Create deny-all hardening files for Apache/IIS and a placeholder index.html.
     */
    private static function writeHardeningFiles($dir)
    {
        // Apache
        $htaccess = $dir . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($htaccess)) {
            $ht = ""
                . "Options -Indexes\n"
                . "<IfModule mod_authz_core.c>\n"
                . "  Require all denied\n"
                . "</IfModule>\n"
                . "<IfModule !mod_authz_core.c>\n"
                . "  Order allow,deny\n"
                . "  Deny from all\n"
                . "</IfModule>\n";
            @file_put_contents($htaccess, $ht);
        }

        // IIS
        $webconfig = $dir . DIRECTORY_SEPARATOR . 'web.config';
        if (!file_exists($webconfig)) {
            $wc = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<configuration>' . "\n"
                . '  <system.webServer>' . "\n"
                . '    <security>' . "\n"
                . '      <authorization>' . "\n"
                . '        <deny users="*" />' . "\n"
                . '      </authorization>' . "\n"
                . '    </security>' . "\n"
                . '    <directoryBrowse enabled="false" />' . "\n"
                . '  </system.webServer>' . "\n"
                . '</configuration>' . "\n";
            @file_put_contents($webconfig, $wc);
        }

        // Placeholder (covers directory listing on some setups)
        $index = $dir . DIRECTORY_SEPARATOR . 'index.html';
        if (!file_exists($index)) {
            @file_put_contents($index, "<!doctype html><title>403</title><h1>Forbidden</h1>");
        }
    }

    /**
     * Sanitize task name to a safe filename component.
     */
    private static function sanitizeTask($task)
    {
        $task = (string)$task;
        $task = preg_replace('/[^A-Za-z0-9._-]/', '_', $task);
        if ($task === '' || $task === null) {
            $task = 'isense';
        }
        return $task;
    }

    public function __construct($task = 'isense', $messageHistoryCount = 20)
    {
        $this->task_name = self::sanitizeTask($task);

        // Allow overriding the runtime directory via WordPress filter if available.
        $dir = self::getProgressDir();
        if (function_exists('apply_filters')) {
            $dir = apply_filters('flmbkp_progress_dir', $dir, $this->task_name);
            if (!is_dir($dir)) {
                if (function_exists('wp_mkdir_p')) {
                    @wp_mkdir_p($dir);
                } else {
                    @mkdir($dir, 0755, true);
                }
                self::writeHardeningFiles($dir);
            }
        }

        $this->progress_file = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->task_name . '.iprogress';

        // Open file for read/write, create if not exists
        $created = !file_exists($this->progress_file);
        $this->fp = @fopen($this->progress_file, 'c+');
        if ($this->fp === false) {
            // As a last resort: try system temp directly
            $fallback = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->task_name . '.iprogress';
            $this->progress_file = $fallback;
            $this->fp = @fopen($this->progress_file, 'c+');
        }

        if (is_resource($this->fp)) {
            if ($created) {
                @chmod($this->progress_file, 0600); // restrict permissions
            }
            $this->loadState();

            $this->message_history_count = ($messageHistoryCount != 20)
                ? (int)$messageHistoryCount
                : (!empty($this->state['history_count']) ? (int)$this->state['history_count'] : (int)$messageHistoryCount);

            $this->max_value     = isset($this->state['max'])         ? (int)$this->state['max']         : 100;
            $this->current_value = isset($this->state['current'])     ? (int)$this->state['current']     : 0;
            $this->messages      = !empty($this->state['messages'])   ? $this->state['messages']         : array();
            $this->last_message  = isset($this->state['last_message'])? $this->state['last_message']     : '';
            $this->abortCalled   = !empty($this->state['abort']);
            $this->data          = !empty($this->state['data'])       ? json_decode($this->state['data'], true) : array();
        } else {
            // If file handle cannot be opened, keep defaults (no progress persistence)
            $this->state = array();
        }
    }

    public function __destruct()
    {
        if (is_resource($this->fp)) {
            @fclose($this->fp);
        }
    }

    public function abort()
    {
        $this->sync();
        $this->abortCalled = true;
        $this->saveState();
    }

    public function abortCalled()
    {
        $this->sync();
        return (bool)$this->abortCalled;
    }

    public function setMax($max)
    {
        $this->sync();
        $this->max_value = (int)$max;
        $this->saveState();
    }

    public function getMax()
    {
        $this->sync();
        return (int)$this->max_value;
    }

    public function setProgress($progress)
    {
        $this->sync();
        $this->current_value = (int)$progress;
        $this->saveState();
    }

    public function getProgress($sync = true)
    {
        if ($sync) {
            $this->sync();
        }
        return (int)$this->current_value;
    }

    public function setData($key, $value)
    {
        $this->sync();
        $this->data[$key] = $value;
        $this->saveState();
    }

    public function getData($key)
    {
        $this->sync();
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function addMsg($msg)
    {
        $this->sync();
        $idx = (int)$this->current_value;
        if (empty($this->messages[$idx])) {
            $this->messages[$idx] = array();
        }
        $this->messages[$idx][] = $msg;
        $this->last_message = $msg;

        if ($this->countMessages() > $this->message_history_count) {
            $this->truncMessages();
        }

        $this->saveState();
    }

    public function getMessages()
    {
        $this->sync();
        return $this->messages;
    }

    public function getLastMessage()
    {
        $this->sync();
        return $this->last_message;
    }

    public function iterateWith($value)
    {
        $this->sync();
        $this->current_value += (int)$value;
        $this->saveState();
    }

    public function getProgressPercent()
    {
        $this->sync();
        if ($this->max_value == 0) {
            return 100;
        }
        return ($this->max_value == $this->current_value)
            ? 100
            : (int)(($this->current_value / $this->max_value) * 100);
    }

    public function clear()
    {
        $this->max_value = 100;
        $this->current_value = 0;
        $this->messages = array();
        $this->last_message = '';
        $this->abortCalled = false;
        $this->data = array();
        $this->saveState();
    }

    private function countMessages()
    {
        $messages_count = 0;
        foreach ($this->messages as $messages) {
            $messages_count += is_array($messages) ? count($messages) : 0;
        }
        return $messages_count;
    }

    private function truncMessages()
    {
        $message_overflow = $this->countMessages() - $this->message_history_count;
        if ($message_overflow <= 0) {
            return;
        }
        foreach ($this->messages as $progress_value => $messages) {
            foreach ($messages as $k => $msg) {
                unset($this->messages[$progress_value][$k]);
                $message_overflow--;
                if ($message_overflow <= 0) {
                    break 2;
                }
            }
            if (empty($this->messages[$progress_value])) {
                unset($this->messages[$progress_value]);
            }
        }
    }

    private function saveState()
    {
        $this->state['max']          = (int)$this->max_value;
        $this->state['current']      = (int)$this->current_value;
        $this->state['messages']     = $this->messages;
        $this->state['last_message'] = (string)$this->last_message;
        $this->state['abort']        = (bool)$this->abortCalled;
        $this->state['history_count']= (int)$this->message_history_count;
        $this->state['data']         = json_encode($this->data);

        if (is_resource($this->fp)) {
            @flock($this->fp, LOCK_EX);
            @ftruncate($this->fp, 0);
            @rewind($this->fp);
            @fwrite($this->fp, json_encode($this->state));
            @fflush($this->fp);
            @flock($this->fp, LOCK_UN);
        }
    }

    private function loadState()
    {
        if (is_resource($this->fp)) {
            @flock($this->fp, LOCK_SH);
            $info = @fstat($this->fp);
            if (!empty($info) && isset($info['size']) && $info['size'] > 0) {
                @rewind($this->fp);
                $raw = @fread($this->fp, $info['size']);
                $decoded = json_decode($raw, true);
                $this->state = is_array($decoded) ? $decoded : array();
            } else {
                $this->state = array();
            }
            @flock($this->fp, LOCK_UN);
        } else {
            $this->state = array();
        }
    }

    private function sync()
    {
        $this->loadState();

        if (!empty($this->state)) {
            if (isset($this->state['max']))          $this->max_value     = (int)$this->state['max'];
            if (isset($this->state['current']))      $this->current_value = (int)$this->state['current'];
            if (isset($this->state['messages']))     $this->messages      = $this->state['messages'];
            if (isset($this->state['last_message'])) $this->last_message  = (string)$this->state['last_message'];
            if (isset($this->state['abort']))        $this->abortCalled   = (bool)$this->state['abort'];
            if (isset($this->state['data']))         $this->data          = json_decode($this->state['data'], true);
            if (isset($this->state['history_count']))$this->message_history_count = (int)$this->state['history_count'];
        }
    }
}
