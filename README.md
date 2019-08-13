[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

<!-- PROJECT LOGO -->
<br />
<p align="center">
  <a href="https://github.com/Softdiscover/softdiscover-db-file-manager">
    <img src="_images/logo.jpg" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">Managefy</h3>

  <p align="center">
    ADVANCED FILE MANAGER FOR WORDPRESS
    <br />
    <a href="https://github.com/Softdiscover/softdiscover-db-file-manager"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/Softdiscover/softdiscover-db-file-manager">View Demo</a>
    ·
    <a href="https://github.com/Softdiscover/softdiscover-db-file-manager/issues">Report Bug</a>
    ·
    <a href="https://github.com/Softdiscover/softdiscover-db-file-manager/issues">Request Feature</a>
  </p>
</p>


<!-- TABLE OF CONTENTS -->
## Table of Contents

* [About the Project](#about-the-project)
  * [Built With](#built-with)
* [Getting Started](#getting-started)
  * [Prerequisites](#prerequisites)
  * [Installation](#installation)
* [Roadmap](#roadmap)
* [Developers](#for-developers)
* [Contributing](#contributing)
* [License](#license)
* [Contact](#contact)
* [Acknowledgements](#acknowledgements)



<!-- ABOUT THE PROJECT -->
## About Managefy
### File manager
[![File Manager][product-screenshot1]](https://softdiscover.com)
### Backup manager
[![Backup Manager][product-screenshot2]](https://softdiscover.com)
### User roles manager
[![User roles manager][product-screenshot3]](https://softdiscover.com)

File Management made easy. Forget using FTP or cPanel! Use our file manager plugin to take full control of your Wordpress website. Managefy provides you the ability to edit, delete, upload, download, copy and paste files and folders. Also Managefy allows to backup your files and database, and restore them too. 

Features:
* Drag and Drop File Management.
* Files / Folder Sharing.
* User Access and User Group Access Control.
* Front-end Access.
* Multiple Themes.
* Image Viewer and Editor.
* Windows like UI.
* Tons of customizations.
* Office and PDF Document Viewer.
* Image thumbnails.
* Zip and Unzip Files / Folders.
* File Chunking Support (Upload Files larger than upload_max_size).
* Icons and List View.
* File Path Hider.

Email-me: info@softdiscover.com

Also, you can use this Project as you wish, be for study, be for make improvements or earn money with it!

It's free!

### Built With
Managefy is built with:
* [Bootstrap](https://getbootstrap.com)
* [JQuery](https://jquery.com)
* [Wordpress](https://wordpress.org)



<!-- GETTING STARTED -->
## Getting Started

For getting started, you just need to download the software from here, then you can install it on your wordpress site like any other wordpress plugin. 

### Prerequisites

* Mysql 5+
* php 5.6+
* Wordpress 5+

### Installation


There are 2 ways to install. Please follow the steps below: 

= Via FTP =
1. After your download unzip `softdiscover-db-file-manager` from your download .zip
2. Open your FTP client
3. Upload the `softdiscover-db-file-manager` folder to /wp-content/plugins/ directory on your hosting server
4. Activate the softdiscover-db-file-manager plugin through the 'Plugins' menu in WordPress
5. Configure the plugin by going to the `Managefy` menu that appears in your admin menu

= Via backend of WordPress =
1. After your download, log into backend of your WordPress 
2. Go to Plugins > Add New
3. Click the Upload link
4. Click Browse and locate the file that you downloaded and click *Install Now*
5. After Wordpress has finished unpacking the file click on *Activate Plugin*
6. After the plugin has been activated you will notice a new menu item on the left hand navigation labelled Managefy
 
<!-- ROADMAP -->
## Roadmap

See the [open issues](https://github.com/Softdiscover/softdiscover-db-file-manager/issues) for a list of proposed features (and known issues).


<!-- DEVELOPERS -->
## For Developers

First, you have to enable debug parameter. go to next file:
*(wordpress dir root)/wp-content/plugins/softdiscover-db-file-manager/db-file-manager.php

and the next code to:
```sh
$this->define('FLMBKP_DEBUG', 1);
```
 
then using powershell, go to next directory:
* (wordpress dir root)/wp-content/plugins/softdiscover-db-file-manager/

and install npm packages
```sh
npm install
```

and run the watch changes
```sh
npm run watch
```

 
<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request



<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE` for more information.



<!-- CONTACT -->
## Contact

Your Name - [@your_twitter](https://twitter.com/softdiscover) - info@softdiscover.com

Project Link: [https://github.com/Softdiscover/softdiscover-db-file-manager](https://github.com/Softdiscover/softdiscover-db-file-manager)



<!-- ACKNOWLEDGEMENTS -->
## Acknowledgements
* [GitHub Emoji Cheat Sheet](https://www.webpagefx.com/tools/emoji-cheat-sheet)
* [GitHub Pages](https://pages.github.com)
* [Animate.css](https://daneden.github.io/animate.css)
* [Font Awesome](https://fontawesome.com)
* [El Finder](https://github.com/Studio-42/elFinder)
* [CodeMirror](https://github.com/codemirror/CodeMirror)




<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/Softdiscover/softdiscover-db-file-manager.svg?style=flat-square
[contributors-url]: https://github.com/Softdiscover/softdiscover-db-file-manager/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/Softdiscover/softdiscover-db-file-manager.svg?style=flat-square
[forks-url]: https://github.com/Softdiscover/softdiscover-db-file-manager/network/members
[stars-shield]: https://img.shields.io/github/stars/Softdiscover/softdiscover-db-file-manager.svg?style=flat-square
[stars-url]: https://github.com/Softdiscover/softdiscover-db-file-manager/stargazers
[issues-shield]: https://img.shields.io/github/issues/Softdiscover/softdiscover-db-file-manager.svg?style=flat-square
[issues-url]: https://github.com/Softdiscover/softdiscover-db-file-manager/issues
[license-shield]: https://img.shields.io/github/license/Softdiscover/softdiscover-db-file-manager.svg?style=flat-square
[license-url]: https://github.com/Softdiscover/softdiscover-db-file-manager/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=flat-square&logo=linkedin&colorB=555
[linkedin-url]: https://www.linkedin.com/company/softdiscover
[product-screenshot1]: _images/screenshot1.png
[product-screenshot2]: _images/screenshot2.png
[product-screenshot3]: _images/screenshot3.png