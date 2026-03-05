<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="keywords" content="pdf,pdf self expiration,pdf self destruction,pdf-drm,pdf online share,pdf encryption,pdf free drm">
  <meta name="description" content="Add free DRM to a PDF file, set the expiration date and limited reading times">
  <meta name="author" content="MaiPDF">

  <title>MaiPDF Offers Free DRM for PDF Files</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="img/favicon.png">
  
  <!-- Social media meta tags -->
  <meta property="og:title" content="MaiPDF - Free DRM for PDF Files">
  <meta property="og:description" content="Add expiration dates and usage limits to your PDF files for free. Works on Windows, Mac, and Android.">
  <meta property="og:image" content="https://www.maipdf.com/img/social-preview.jpg">
  <meta property="og:url" content="https://www.maipdf.com/">
  <meta name="twitter:card" content="summary_large_image">

  <!-- Language Translations -->
  <script>
    // Define translations for all text elements
    const translations = {
      'en': { // English
        'title': 'MaiPDF DRM for PDF',
        'subtitle': 'Encrypted PDF File with Digital Key<br>HTML extension Made it easy on MAC/PC',
        'cta_button': 'Ever used executable version of a PDF?<br>Try our Free MaiPDF',
        'upload_title': 'First Step: Upload your PDF file',
        'upload_label': 'Please Upload PDF Files (Maximum Size: 90MB)',
        'upload_note': 'Supported formats: PDF, HTML. Your files are secure and will be automatically deleted after 30 days.',
        'dropzone_message': 'Drag or Click to Upload',
        'success_message': 'Upload Successful',
        'settings_title': 'Second Step: Configure DRM Protection',
        'settings_subtitle': 'Your document will expire when any of these conditions are met',
        'platform_note': 'All settings work on Windows, Mac, and Android devices (iOS not supported)',
        'file_name': 'Selected File',
        'open_limit': 'Open Limit',
        'open_limit_tooltip': 'Set how many times the file can be opened before expiring',
        'open_limit_placeholder': 'Enter number',
        'open_limit_unit': 'times',
        'expiration_period': 'Expiration Period',
        'expiration_tooltip': 'Set how many days until the file expires',
        'expiration_placeholder': 'Enter days',
        'expiration_unit': 'days',
        'generate_button': 'Generate Protected File',
        'apply_button': 'Apply Protection',
        'download_title': 'Your Protected File is Ready',
        'success_alert': 'Success! Your PDF has been protected with the restrictions you specified.',
        'security_codes': 'Your Security Codes',
        'reading_code': 'Reading Code:',
        'modification_code': 'Modification Code:',
        'security_note': 'Save these codes if you need to modify your file settings later',
        'protections': 'Applied Protections',
        'expires_on': 'Expires on:',
        'max_opens': 'Max opens:',
        'manage_button': 'Manage File Settings',
        'download_button': 'Download Protected File',
        'support_button': 'Support This Project',
        'how_to_use': 'How to Use Your Protected File',
        'how_to_step1': 'Download the protected file (ZIP format)',
        'how_to_step2': 'Extract the ZIP file to your computer',
        'how_to_step3': 'Open the extracted HTML file with any modern web browser',
        'how_to_step4': 'Your PDF content will load with the applied protections',
        'how_to_step5': 'The file will stop working once any protection limit is reached',
        'how_to_note': 'Works on Windows, Mac, and Android devices. Not compatible with iOS.',
        'progress_step1': 'Step 1 of 3',
        'progress_step2': 'Step 2 of 3',
        'progress_complete': 'Complete!',
        'upload_first': 'Click \'Upload\' in Step One',
        'copied': 'Copied!',
        'nav_upload': 'Upload',
        'nav_drm': 'DRM Setting',
        'nav_file': 'Your File',
        'nav_qr': 'QR Tools'
      },
      'zh': { // Chinese
        'title': 'MaiPDF PDF文件数字版权管理',
        'subtitle': '使用数字密钥加密PDF文件<br>HTML扩展使其在MAC/PC上易于使用',
        'cta_button': '曾经使用过PDF的可执行版本吗？<br>试试我们的免费MaiPDF',
        'upload_title': '第一步：上传您的PDF文件',
        'upload_label': '请上传PDF文件（最大尺寸：90MB）',
        'upload_note': '支持的格式：PDF、HTML。您的文件安全且将在30天后自动删除。',
        'dropzone_message': '拖放或点击上传',
        'success_message': '上传成功',
        'settings_title': '第二步：配置DRM保护',
        'settings_subtitle': '当满足以下任一条件时，您的文档将过期',
        'platform_note': '所有设置适用于Windows、Mac和Android设备（iOS不支持）',
        'file_name': '已选择文件',
        'open_limit': '打开次数限制',
        'open_limit_tooltip': '设置文件可以被打开的次数',
        'open_limit_placeholder': '输入数字',
        'open_limit_unit': '次',
        'expiration_period': '过期时间',
        'expiration_tooltip': '设置文件过期的天数',
        'expiration_placeholder': '输入天数',
        'expiration_unit': '天',
        'generate_button': '生成受保护文件',
        'apply_button': '应用保护',
        'download_title': '您的受保护文件已准备就绪',
        'success_alert': '成功！您的PDF已按指定限制进行保护。',
        'security_codes': '您的安全代码',
        'reading_code': '阅读码：',
        'modification_code': '修改码：',
        'security_note': '如果您需要修改文件设置，请保存这些代码',
        'protections': '已应用的保护',
        'expires_on': '过期日期：',
        'max_opens': '最大打开次数：',
        'manage_button': '管理文件设置',
        'download_button': '下载受保护文件',
        'support_button': '支持此项目',
        'how_to_use': '如何使用您的受保护文件',
        'how_to_step1': '下载受保护的文件（ZIP格式）',
        'how_to_step2': '将ZIP文件解压到您的计算机',
        'how_to_step3': '使用任何现代浏览器打开解压后的HTML文件',
        'how_to_step4': '您的PDF内容将加载并应用保护限制',
        'how_to_step5': '当达到任何保护限制时，文件将停止工作',
        'how_to_note': '适用于Windows、Mac和Android设备。不兼容iOS。',
        'progress_step1': '第1步（共3步）',
        'progress_step2': '第2步（共3步）',
        'progress_complete': '完成！',
        'upload_first': '请先在第一步中上传文件',
        'copied': '已复制！',
        'nav_upload': '上传',
        'nav_drm': 'DRM设置',
        'nav_file': '您的文件',
        'nav_qr': '二维码工具'
      },
      'es': { // Spanish
        'title': 'MaiPDF DRM para PDF',
        'subtitle': 'Archivo PDF cifrado con clave digital<br>Extensión HTML para facilitar su uso en MAC/PC',
        'cta_button': '¿Alguna vez has usado una versión ejecutable de un PDF?<br>Prueba nuestro MaiPDF Gratis',
        'upload_title': 'Primer paso: Sube tu archivo PDF',
        'upload_label': 'Por favor sube archivos PDF (Tamaño máximo: 90MB)',
        'upload_note': 'Formatos soportados: PDF, HTML. Tus archivos están seguros y serán eliminados automáticamente después de 30 días.',
        'dropzone_message': 'Arrastra o haz clic para subir',
        'success_message': 'Subida exitosa',
        'settings_title': 'Segundo paso: Configura la protección DRM',
        'settings_subtitle': 'Tu documento caducará cuando se cumpla cualquiera de estas condiciones',
        'platform_note': 'Todas las configuraciones funcionan en dispositivos Windows, Mac y Android (iOS no compatible)',
        'file_name': 'Archivo seleccionado',
        'open_limit': 'Límite de aperturas',
        'open_limit_tooltip': 'Establece cuántas veces se puede abrir el archivo antes de caducar',
        'open_limit_placeholder': 'Ingresa número',
        'open_limit_unit': 'veces',
        'expiration_period': 'Período de caducidad',
        'expiration_tooltip': 'Establece cuántos días hasta que el archivo caduque',
        'expiration_placeholder': 'Ingresa días',
        'expiration_unit': 'días',
        'generate_button': 'Generar archivo protegido',
        'apply_button': 'Aplicar protección',
        'download_title': 'Tu archivo protegido está listo',
        'success_alert': '¡Éxito! Tu PDF ha sido protegido con las restricciones especificadas.',
        'security_codes': 'Tus códigos de seguridad',
        'reading_code': 'Código de lectura:',
        'modification_code': 'Código de modificación:',
        'security_note': 'Guarda estos códigos si necesitas modificar la configuración del archivo más tarde',
        'protections': 'Protecciones aplicadas',
        'expires_on': 'Caduca el:',
        'max_opens': 'Aperturas máximas:',
        'manage_button': 'Administrar configuración',
        'download_button': 'Descargar archivo protegido',
        'support_button': 'Apoyar este proyecto',
        'how_to_use': 'Cómo usar tu archivo protegido',
        'how_to_step1': 'Descarga el archivo protegido (formato ZIP)',
        'how_to_step2': 'Extrae el archivo ZIP a tu computadora',
        'how_to_step3': 'Abre el archivo HTML extraído con cualquier navegador moderno',
        'how_to_step4': 'Tu contenido PDF se cargará con las protecciones aplicadas',
        'how_to_step5': 'El archivo dejará de funcionar cuando se alcance cualquier límite de protección',
        'how_to_note': 'Funciona en dispositivos Windows, Mac y Android. No compatible con iOS.',
        'progress_step1': 'Paso 1 de 3',
        'progress_step2': 'Paso 2 de 3',
        'progress_complete': '¡Completo!',
        'upload_first': 'Haz clic en \'Subir\' en el primer paso',
        'copied': '¡Copiado!',
        'nav_upload': 'Subir',
        'nav_drm': 'Configuración DRM',
        'nav_file': 'Tu Archivo',
        'nav_qr': 'Herramientas QR'
      },
      'fr': { // French
        'title': 'MaiPDF DRM pour PDF',
        'subtitle': 'Fichier PDF crypté avec clé numérique<br>Extension HTML pour faciliter l\'utilisation sur MAC/PC',
        'cta_button': 'Avez-vous déjà utilisé une version exécutable d\'un PDF?<br>Essayez notre MaiPDF gratuit',
        'upload_title': 'Première étape : Téléchargez votre fichier PDF',
        'upload_label': 'Veuillez télécharger des fichiers PDF (Taille maximale : 90MB)',
        'upload_note': 'Formats pris en charge : PDF, HTML. Vos fichiers sont sécurisés et seront automatiquement supprimés après 30 jours.',
        'dropzone_message': 'Glissez ou cliquez pour télécharger',
        'success_message': 'Téléchargement réussi',
        'settings_title': 'Deuxième étape : Configurez la protection DRM',
        'settings_subtitle': 'Votre document expirera lorsque l\'une de ces conditions sera remplie',
        'platform_note': 'Tous les paramètres fonctionnent sur les appareils Windows, Mac et Android (iOS non pris en charge)',
        'file_name': 'Fichier sélectionné',
        'open_limit': 'Limite d\'ouvertures',
        'open_limit_tooltip': 'Définissez combien de fois le fichier peut être ouvert avant d\'expirer',
        'open_limit_placeholder': 'Entrez un nombre',
        'open_limit_unit': 'fois',
        'expiration_period': 'Période d\'expiration',
        'expiration_tooltip': 'Définissez le nombre de jours avant l\'expiration du fichier',
        'expiration_placeholder': 'Entrez des jours',
        'expiration_unit': 'jours',
        'generate_button': 'Générer un fichier protégé',
        'apply_button': 'Appliquer la protection',
        'download_title': 'Votre fichier protégé est prêt',
        'success_alert': 'Succès ! Votre PDF a été protégé avec les restrictions spécifiées.',
        'security_codes': 'Vos codes de sécurité',
        'reading_code': 'Code de lecture :',
        'modification_code': 'Code de modification :',
        'security_note': 'Enregistrez ces codes si vous devez modifier les paramètres du fichier ultérieurement',
        'protections': 'Protections appliquées',
        'expires_on': 'Expire le :',
        'max_opens': 'Ouvertures maximales :',
        'manage_button': 'Gérer les paramètres',
        'download_button': 'Télécharger le fichier protégé',
        'support_button': 'Soutenir ce projet',
        'how_to_use': 'Comment utiliser votre fichier protégé',
        'how_to_step1': 'Téléchargez le fichier protégé (format ZIP)',
        'how_to_step2': 'Extrayez le fichier ZIP sur votre ordinateur',
        'how_to_step3': 'Ouvrez le fichier HTML extrait avec n\'importe quel navigateur moderne',
        'how_to_step4': 'Votre contenu PDF se chargera avec les protections appliquées',
        'how_to_step5': 'Le fichier cessera de fonctionner lorsque n\'importe quelle limite de protection sera atteinte',
        'how_to_note': 'Fonctionne sur les appareils Windows, Mac et Android. Non compatible avec iOS.',
        'progress_step1': 'Étape 1 sur 3',
        'progress_step2': 'Étape 2 sur 3',
        'progress_complete': 'Terminé !',
        'upload_first': 'Cliquez sur \'Télécharger\' dans la première étape',
        'copied': 'Copié !',
        'nav_upload': 'Télécharger',
        'nav_drm': 'Paramètres DRM',
        'nav_file': 'Votre Fichier',
        'nav_qr': 'Outils QR'
      }
    };
    
    // Function to update the displayed language name in the dropdown
    function updateLanguageDisplay(languageName) {
      document.getElementById('current-language').innerText = languageName;
    }
    
    // Default language
    let currentLang = 'en';
    
    // Function to set page language
    function setLanguage(lang) {
      currentLang = lang;
      
      // Store the selected language in localStorage
      localStorage.setItem('maipdf_language', lang);
      
      // Update all text elements with their translations
      document.querySelectorAll('[data-translate]').forEach(element => {
        const key = element.getAttribute('data-translate');
        if (translations[lang] && translations[lang][key]) {
          if (element.tagName === 'INPUT' && element.getAttribute('type') === 'submit') {
            element.value = translations[lang][key];
          } else if (element.tagName === 'INPUT' && element.getAttribute('placeholder')) {
            element.setAttribute('placeholder', translations[lang][key]);
          } else if (element.getAttribute('data-toggle') === 'tooltip') {
            element.setAttribute('title', translations[lang][key]);
            // Reinitialize tooltips if Bootstrap is loaded
            if (typeof $ !== 'undefined') {
              $(element).tooltip('dispose').tooltip();
            }
          } else {
            element.innerHTML = translations[lang][key];
          }
        }
      });
      
      // Update document title
      document.title = 'MaiPDF - ' + translations[lang]['title'];
      
      // Update dropzone text if defined
      if (typeof myDropzone !== 'undefined') {
        myDropzone.options.dictDefaultMessage = translations[lang]['dropzone_message'] || 'Drag or Click to Upload';
        myDropzone.options.dictMaxFilesExceeded = "You cannot upload any more files.";
        myDropzone.options.dictResponseError = "Server error.";
        myDropzone.options.dictInvalidFileType = "Invalid file type.";
        myDropzone.options.dictFallbackMessage = "Your browser does not support drag'n'drop uploads.";
        myDropzone.options.dictFileTooBig = "File is too big.";
        myDropzone.options.dictRemoveFile = "Remove";
        myDropzone.options.dictCancelUpload = "Cancel";
      }
      
      // Update progress bar text based on current step
      updateProgressBarText();
    }
    
    // Update progress bar text according to the current language
    function updateProgressBarText() {
      const progressBar = document.getElementById('progress-bar');
      if (!progressBar) return;
      
      const currentWidth = progressBar.style.width;
      if (currentWidth === '33%') {
        progressBar.innerHTML = translations[currentLang]['progress_step1'];
      } else if (currentWidth === '66%') {
        progressBar.innerHTML = translations[currentLang]['progress_step2'];
      } else if (currentWidth === '100%') {
        progressBar.innerHTML = translations[currentLang]['progress_complete'];
      }
    }
    
    // Check if the user has a saved language preference
    document.addEventListener('DOMContentLoaded', function() {
      const savedLang = localStorage.getItem('maipdf_language');
      if (savedLang && translations[savedLang]) {
        setLanguage(savedLang);
        updateLanguageDisplay(document.querySelector(`.dropdown-item[onclick*="setLanguage('${savedLang}')"]`).textContent.trim());
      } else {
        // Try to detect browser language
        const browserLang = navigator.language || navigator.userLanguage;
        const langCode = browserLang.split('-')[0];
        
        if (translations[langCode]) {
          setLanguage(langCode);
          updateLanguageDisplay(document.querySelector(`.dropdown-item[onclick*="setLanguage('${langCode}')"]`).textContent.trim());
          
          // Show browser language detection notification
          showLanguageNotification(langCode);
        } else {
          setLanguage('en'); // Default to English
        }
      }
    });
    
    // Function to show a notification about detected language
    function showLanguageNotification(langCode) {
      // Create language names map
      const languageNames = {
        'en': 'English',
        'zh': '中文 (Chinese)',
        'es': 'Español (Spanish)',
        'fr': 'Français (French)'
      };
      
      // Only show notification if it's not English (default)
      if (langCode !== 'en') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'language-notification';
        notification.innerHTML = `
          <div class="notification-content">
            <i class="fas fa-language"></i> 
            <span>${translations[langCode].language_detected}</span>
            <button class="btn btn-sm btn-outline-light ml-2" onclick="dismissNotification()">${translations[langCode].ok_button}</button>
          </div>
        `;
        
        // Add notification styles
        const style = document.createElement('style');
        style.textContent = `
          .language-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: rgba(70, 130, 180, 0.9);
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideIn 0.5s forwards;
            font-size: 14px;
          }
          .notification-content {
            display: flex;
            align-items: center;
          }
          .notification-content i {
            margin-right: 10px;
            font-size: 1.2em;
          }
          @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
          }
          @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
          }
        `;
        
        // Add to document
        document.head.appendChild(style);
        document.body.appendChild(notification);
        
        // Dismiss after 8 seconds
        setTimeout(function() {
          dismissNotification();
        }, 8000);
      }
    }
    
    // Function to dismiss the language notification
    function dismissNotification() {
      const notification = document.querySelector('.language-notification');
      if (notification) {
        notification.style.animation = 'slideOut 0.5s forwards';
        setTimeout(function() {
          notification.remove();
        }, 500);
      }
    }
  </script>

<link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.13.1/css/all.min.css" rel="stylesheet" type="text/css">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9224406325142860" crossorigin="anonymous"></script>


  <!-- Plugin CSS -->
  <link href="vendor/magnific-popup/magnific-popup.css" rel="stylesheet">

  <!-- Theme CSS - Includes Bootstrap -->
  <link href="css/creative.min.css" rel="stylesheet">
<script src="js/dropzone/dropzone.js"></script>
<script src="js/dropzone/dropzone-amd-module.js"></script>
    <link rel="stylesheet" href="js/dropzone/dropzone.css">
<link rel="stylesheet" href="js/dropzone/basic.css">


</head>

<body id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" style="color: Blue;" href="../#page-top">MaiPDF</a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        
		<span style="color: Red;"> <i class="fas  fa-th "></i></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto my-2 my-lg-0">
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#about" data-translate="nav_upload">Upload</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" id="anquan" href="#services" data-translate="nav_drm">DRM Setting</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#contact" data-translate="nav_file">YourFile</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="https://www.maipdf.com/qr.php" data-translate="nav_qr">QR Tools</a>
          </li>
          <!-- Language Selector Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-globe"></i> <span id="current-language">English</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languageDropdown">
              <a class="dropdown-item" href="#" onclick="setLanguage('en'); updateLanguageDisplay('English'); return false;">English</a>
              <a class="dropdown-item" href="#" onclick="setLanguage('zh'); updateLanguageDisplay('中文'); return false;">中文 (Chinese)</a>
              <a class="dropdown-item" href="#" onclick="setLanguage('es'); updateLanguageDisplay('Español'); return false;">Español (Spanish)</a>
              <a class="dropdown-item" href="#" onclick="setLanguage('fr'); updateLanguageDisplay('Français'); return false;">Français (French)</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Progress Bar - New Addition -->
  <div class="progress-container fixed-top" style="top: 56px; position: fixed; width: 100%; z-index: 1030;">
    <div class="progress">
      <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">Step 1 of 3</div>
    </div>
  </div>
  
  <script>
  // Function to update progress bar based on the current section in view or form state
  function updateProgressBar() {
    // Check if we just processed a form submission (indicated by presence of form data)
    var justSubmitted = <?php echo !empty($_POST) ? 'true' : 'false'; ?>;
    
    // Get the hash from URL
    var currentHash = window.location.hash;
    
    // If form was just submitted, we're likely at step 3
    if (justSubmitted && (currentHash === "#contact" || !currentHash)) {
      document.getElementById('progress-bar').style.width = '100%';
      document.getElementById('progress-bar').setAttribute('aria-valuenow', '100');
      document.getElementById('progress-bar').innerHTML = 'Complete!';
      return;
    }
    
    // If no hash and no form submission, we're at step 1
    if (!currentHash && !justSubmitted) {
      document.getElementById('progress-bar').style.width = '33%';
      document.getElementById('progress-bar').setAttribute('aria-valuenow', '33');
      document.getElementById('progress-bar').innerHTML = 'Step 1 of 3';
      return;
    }
    
    // Otherwise, determine by hash
    currentHash = currentHash.replace("#", "");
    
    if (currentHash === "about") {
      document.getElementById('progress-bar').style.width = '33%';
      document.getElementById('progress-bar').setAttribute('aria-valuenow', '33');
      document.getElementById('progress-bar').innerHTML = 'Step 1 of 3';
    } else if (currentHash === "services") {
      document.getElementById('progress-bar').style.width = '66%';
      document.getElementById('progress-bar').setAttribute('aria-valuenow', '66');
      document.getElementById('progress-bar').innerHTML = 'Step 2 of 3';
    } else if (currentHash === "contact") {
      document.getElementById('progress-bar').style.width = '100%';
      document.getElementById('progress-bar').setAttribute('aria-valuenow', '100');
      document.getElementById('progress-bar').innerHTML = 'Complete!';
    }
  }
  
  // Run on page load
  document.addEventListener('DOMContentLoaded', updateProgressBar);
  
  // Also run when the hash changes (user navigates between sections)
  window.addEventListener('hashchange', updateProgressBar);
  </script>
  
  <!-- Masthead -->
  
  <header class="masthead">
    <div class="container h-100">
      <div class="row h-100 align-items-center justify-content-center text-center">
        <div class="col-lg-10 align-self-end">
          <h1 id='diyi' class="text-white font-weight-bold" data-translate="title">MaiPDF DRM for PDF</h1>
          <hr class="divider my-4">
        </div>
        <div class="col-lg-8 align-self-baseline">
          <p class="text-white-75 font-weight-light mb-5" data-translate="subtitle">Encrypted PDF File with Digital Key<br>HTML extension Made it easy on MAC/PC</p>
           
		  <h2>
	       <a class="btn btn-info btn-xl js-scroll-trigger" style="text-transform: none;" href="#about" data-translate="cta_button">Ever used executable version of a PDF?<br>Try our Free MaiPDF</a>
		  </h2>

        </div
 
      </div>

    </div>
  </header>



  <!-- About Section <div class="alert alert-danger">德国网站,国内速度慢</div>-->
  <section class="page-section bg-primary" id="about">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="text-white mt-0" data-translate="upload_title">First Step: Upload your PDF file</h2>  

<a href="http://doc.maitube.club/js/constitution.html.zip" style="color:green">Example1</a>

           <hr class="divider my-4">
		  
   
		<div class="form-group">
    <label class="title" id="pleaseupload" data-translate="upload_label">Please Upload PDF Files (Maximum Size: 90MB)</label>
    <div id="dropz" class="dropzone" style="font-weight:900;border-style:dashed;border-width:5px;background: linear-gradient(135deg, #6bc5f8, #9198e5); padding: 20px; border-radius: 10px;"> </div>
    <div class="text-center mt-3">
      <small class="text-white" data-translate="upload_note">Supported formats: PDF, HTML. Your files are secure and will be automatically deleted after 30 days.</small>
    </div>
</div>
<input type="hidden" name="file_id" ng-model="file_id" id="file_id"/>


<script>
var appElement = document.querySelector('div .inmodal');
    var myDropzone = new Dropzone("#dropz", {
        url: "drmai.php",//文件提交地址
        method:"post",  //也可用put
        paramName:"file", //默认为file
        maxFiles:1,//一次性上传的文件数量上限
        maxFilesize: 222, //文件大小，单位：MB
		//chunking: true,
		//forceChunking: true,
		//chunkSize: 256000,
		//retryChunks: true,
        //retryChunksLimit: 3,
        acceptedFiles: ".png,.jpg,.jpeg,.gif,image/*,.pdf",
        addRemoveLinks:true,
		addRemoveLinks:true,
		retryChunksLimit: 3,
        parallelUploads: 1,//一次上传的文件数量
        dictDefaultMessage:'Drag or Click to Upload',
        dictMaxFilesExceeded: "One File！",
        dictResponseError: 'Fail!',
        dictInvalidFileType: "with*.pdf,*.png,*.jpeg。",
        dictFallbackMessage:"you are using antiqued Browser",
        dictFileTooBig:"Over Limit",
        dictRemoveLinks: "Del",
        dictCancelUpload: "Cancel",
		timeout: 192000,
        init:function(){
            this.on("addedfile", function(file) {
                //上传文件时触发的事件
                document.querySelector('div .dz-default').style.display = 'none';
            });
            this.on("success",function(file,data){
                //上传成功触发的事件
               // console.log('ok');
				//alert(file.name);
				//$('#about').hide();
				//$('#tou').hide();
				//$('#contact').hide();
				document.getElementById('anquan').click();
				<?php

        ini_set("display_errors", true);
         ini_set("html_errors", true); 
				     $year=  date("Y");
					$month= date("m");
					$week=  date("d");
					$fileplace="/".$year."/".$month."/".$week."/offline/";
				?>
				var a = "<?php echo $fileplace ?>" + file.name;
				document.getElementById("name").value = a;
				document.getElementById("pleaseupload").innerHTML = "Upload Successful";
				
				// Just update the progress bar without disrupting the click navigation
				setTimeout(function() {
				    document.getElementById('progress-bar').style.width = '66%';
				    document.getElementById('progress-bar').setAttribute('aria-valuenow', '66');
				    document.getElementById('progress-bar').innerHTML = 'Step 2 of 3';
				}, 300);
				
                angular.element(appElement).scope().file_id = data.data.id;
                
            });
            this.on("error",function (file,data) {
                //上传失败触发的事件
                console.log('fail');
                var message = '';
                //lavarel框架有一个表单验证，
                //对于ajax请求，JSON 响应会发送一个 422 HTTP 状态码，
                //对应file.accepted的值是false，在这里捕捉表单验证的错误提示
                if (file.accepted){
                    $.each(data,function (key,val) {
                        message = message + val[0] + ';';
                    })
                    //控制器层面的错误提示，file.accepted = true的时候；
                    alert(message);
                }
            });
            this.on("removedfile",function(file){
                //删除文件时触发的方法
                var file_id = angular.element(appElement).scope().file_id;
                if (file_id){
                    $.post('/admin/del/'+ file_id,{'_method':'DELETE'},function (data) {
                        console.log('删除结果:'+data.message);
                    })
                }
                angular.element(appElement).scope().file_id = 0;
                document.querySelector('div .dz-default').style.display = 'block';
            });
        }
    });

</script>
		 
		 
		 <?php
			//设置成东八区时间
			//print_r($_FILES);
			$identifier='';
			if(!empty($_FILES) ){
			//date_default_timezone_set('etc/gmt-8');
			$allowedExts = array("pdf", "htm", "html");
			$temp = explode(".", $_FILES["file"]["name"]);
			//echo $_FILES["file"]["size"];
			$extension = end($temp);     // 获取文件后缀名
			$filename = $_FILES["file"]["name"];

			//echo $extension;
			if (($_FILES["file"]["size"] < 118097152)  && in_array($extension, $allowedExts))
				{
				if ($_FILES["file"]["error"] > 0)
				{
					//echo "错误：: " . $_FILES["file"]["error"] . "<br>";
					 die('File beyond 2M');
				}
				else
				{
					$year= date("Y");
					$month= date("m");
					$week=  date("d");
					$fileplace="yes/".$year."/".$month."/".$week."/"."offline/";
					if (!is_dir($fileplace)){
					  if (!mkdir($fileplace, 0777, true)) {
						  die('Failed to create folders...');
					  }
					}
					//echo $fileplace;

					else
					{
						
	         if (file_exists($fileplace . $_FILES["file"]["name"]))
					{
						unlink($fileplace.$_FILES["file"]["name"]);
					}
						// 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
							move_uploaded_file($_FILES["file"]["tmp_name"], $fileplace. $_FILES["file"]["name"]);
							//echo "文件存储在: " . $fileplace . $_FILES["file"]["name"];
							echo "Upload Successfully, please set up your reading restriction";
							$url=$fileplace.$_FILES["file"]["name"];
							//session_start();
						  // $_SESSION["url"] = $url;
						      require_once __DIR__ . '/vendorJiami/autoload.php';
								try{
								$mpdf = new \Mpdf\Mpdf();

								$pagecount = $mpdf->SetSourceFile($fileplace. $_FILES["file"]["name"]);

								for($i=1;$i<=$pagecount;$i++){
									 $mpdf->AddPage();
									 $tplId3 = $mpdf->ImportPage($i);
									 $size=$mpdf->getTemplateSize($tplId3);
									 $mpdf->UseTemplate($tplId3,0,0,$size['width'],$size['height'],true);								
								}
								$mpdf->SetProtection(array(), 'guaguashimaimai', 'guaguashimaimai');
								$mpdf->Output($fileplace. $_FILES["file"]["name"], \Mpdf\Output\Destination::FILE);						
								}
								catch(Exception $e)
								 {
								 echo 'Message: ' .$e->getMessage();
								// echo "~~~~";
								 }
						}
					}
				}
				else
					{
						die('Beyond 5M');
					}
			  }else{
					//echo "请上传90M内大小文件";
			  }	  
			?>

        </div>
      </div>
    </div>

  </section>
  
  <!-- Services Section -->
  <section class="page-section" id="services">
    <div class="container">

	<form role="form" action="drmai.php#contact" method="post">
      <h2 class="text-center mt-0" data-translate="settings_title">Second Step: Configure DRM Protection</h2>
	  <h5 class="text-center mt-0" style="color:blue;" data-translate="settings_subtitle">Your document will expire when any of these conditions are met</h5>
      <div class="alert alert-info text-center">
        <i class="fas fa-info-circle mr-2"></i> <span data-translate="platform_note">All settings work on Windows, Mac, and Android devices (iOS not supported)</span>
      </div>
      <hr class="divider my-4">
      <div class="row">
	  
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-file-pdf text-primary mb-4"></i>
            <h5 class="h4 mb-2" style="font-size:1.2em;font-weight:bold;" data-translate="file_name">Selected File</h5>
            <p class="text-muted mb-0">	
			<input type="text" class="form-control" id="name" name="sender"
			 value="<?php empty($_FILES)? print "Click 'Upload' in Step One": print "/".$year."/".$month."/".$week."/".$_FILES["file"]["name"]; ?>" readonly="readonly">
			</p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-folder-open text-primary mb-4"></i>
            <h5 class="h4 mb-2" style="font-size:1.2em;font-weight:bold;" data-translate="open_limit">
              Open Limit
              <span data-toggle="tooltip" data-placement="top" title="Set how many times the file can be opened before expiring" data-translate="open_limit_tooltip">
                <i class="fas fa-question-circle small text-info"></i>
              </span>
            </h5>
            <p class="text-muted mb-0">
              <div class="input-group">
                <input class="form-control" type="number" name="limit" placeholder="Enter number" min="1" max="999" data-translate="open_limit_placeholder">
                <div class="input-group-append">
                  <span class="input-group-text" data-translate="open_limit_unit">times</span>
                </div>
              </div>
            </p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-hourglass-start text-primary mb-4"></i>
            
			<h5 class="h4 mb-2" style="font-size:1.2em;font-weight:bold;" data-translate="expiration_period">
              Expiration Period
              <span data-toggle="tooltip" data-placement="top" title="Set how many days until the file expires" data-translate="expiration_tooltip">
                <i class="fas fa-question-circle small text-info"></i>
              </span>
            </h5>
            <p class="text-muted mb-0">
              <div class="input-group">
                <input class="form-control" type="number" name="password" placeholder="Enter days" min="1" max="1000" data-translate="expiration_placeholder">
                <div class="input-group-append">
                  <span class="input-group-text" data-translate="expiration_unit">days</span>
                </div>
              </div>
            </p>
          </div>
        </div>
        <div class="col-lg-3 col-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-shield-alt text-primary mb-4"></i>
			<h5 class="h4 mb-2" style="font-size:1.2em;font-weight:bold;" data-translate="generate_button">Generate Protected File</h5>
           
			<p class="text-muted mb-0"><input type="submit" value="Apply Protection" class="btn btn-success btn-lg" data-translate="apply_button"></p>
          </div>
        </div>
      </div>
	  </form>

	<?php
if (isset($_POST['limit'])) {
    $limit = htmlspecialchars($_POST['limit']);

    if (isset($_POST['password'])) {
        $password = htmlspecialchars($_POST['password']);

        // If password or limit are less than 1, exit and show warning
        if ($password < 1 || $limit < 1) {
            exit("<script>
                document.getElementById(\"diyi\").className=\"text-warning\";
                document.getElementById(\"diyi\").innerHTML = 
                    \"Too Short for Setting of Each Reading Period<br>Don't Joke with our IT. He's Been MAD\";
            </script>");
        }

        // Get the sender URL and generate an identifier
        $url = $_POST['sender'];
        $keyurl = $_POST['sender'];
        $identifier = $url . rand(0, 100);
        $identifier = md5($identifier);

        // Limit password to a maximum of 999
        if ($password > 1000) {
            $password = 999;
        }

        // File handling logic
        $dname = 'joe' . $url;
        $url = explode('.pdf', $url);
        $downloadname = explode('/', $dname);

        // Start modifying the uploaded file
        $keypdf = 'yes' . $keyurl;
        $coder = base64_encode(file_get_contents($keypdf));
        
        // Substring from the coder
        $firstPartcoder = substr($coder, 0, -399);
        
        // Modify the identifier
        $identifier = $coder[150] . $coder[170] . substr($identifier, 2);
        
        // Define file paths
        $yanhuo = 'yes/' . $year . '/' . $month . '/' . $week . '/offline/' . $identifier . '.csv';
        $yanhuopdf = 'yes' . $url[0] . '.html';
        $yanhuozip = 'yes' . $url[0] . '.zip';
        $yanhuozipname = explode('/', $yanhuopdf);
        $c = count($yanhuozipname) - 1;
        $yanhuozipname = $yanhuozipname[$c];

        // Delete existing files if they exist
        if (file_exists($yanhuopdf)) {
            unlink($yanhuopdf);
        }
        if (file_exists($yanhuozip)) {
            unlink($yanhuozip);
        }

        // Create new CSV file
        $file = fopen($yanhuo, 'w+');
        $jieguo = [];
        $jieguo[0] = $limit;
        $jieguo[1] = $password;
        $jieguo[2] = date("Y-m-d", strtotime("+$jieguo[1] day"));
        $jieguo[3] = $url[0];

        // Get the last 399 characters of the encoded data
        $last399Chars = substr($coder, -399);
        $jieguo[4] = $last399Chars;

        // Insert date validation logic
        $today = date("Y-m-d");
        if (strtotime($today) < strtotime($jieguo[2])) {
            // Add extra logic if necessary
        }

        // Write the data to the CSV
        fputcsv($file, $jieguo);
        fclose($file);

        // Prepare JavaScript data
        $jsyanhuo = $year . '*' . $month . '*' . $week . '*' . $identifier;
        $part0 = "<script> var beforejieguo = '" . $jsyanhuo . "'; filenameofthisfile='" . $downloadname[5] . "'; var pdfData = '".$firstPartcoder ."';</script>";
        //$wholebase = " var pdfData = '" . $firstPartcoder . "'; ";

        // Write the data to the PDF file
        $file = fopen($yanhuopdf, 'w');
        $part1 = file_get_contents("img/newpart1.txt");
        $part3 = file_get_contents("img/newpart3.txt");

        try {
            fwrite($file, $part0);
            fwrite($file, $part1);
           // fwrite($file, $wholebase);
           // fwrite($file, $part3);
            fclose($file);
        } catch (Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }

        // Create a zip archive containing the PDF
        $zip = new ZipArchive;
        $zip->open($yanhuozip, ZipArchive::CREATE);
        $zip->addFile($yanhuopdf, $yanhuozipname);
        $zip->close();

        echo $yanhuozipname;
    }
}
?>
     <!--  <h2 class="mb-4">Your Reading is Created</h2>-->
  <!-- Contact Section -->
  <section class="page-section" id="contact">
    <div class="container">

      <div class="row justify-content-center">
        <div class="col-lg-10 text-center">
          <div class="card shadow-lg border-0 mb-5">
            <div class="card-body py-5">
              <h2 class="mb-4">
                <i class="fas fa-check-circle text-success mr-2"></i> <span data-translate="download_title">Your Protected File is Ready</span>
              </h2>
              
              <div class="alert alert-success mb-4">
                <span data-translate="success_alert">Success! Your PDF has been protected with the restrictions you specified.</span>
              </div>
              
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="card bg-light mb-3">
                    <div class="card-header">
                      <i class="fas fa-key mr-2"></i> <span data-translate="security_codes">Your Security Codes</span>
                    </div>
                    <div class="card-body">
                      <p class="card-text">
                        <strong data-translate="reading_code">Reading Code:</strong> 
                        <span id="readingCode" class="text-monospace"><?php echo isset($identifier) ? $identifier : 'N/A'; ?></span>
                        <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyToClipboard('readingCode')">
                          <i class="fas fa-copy"></i>
                        </button>
                      </p>
                      <p class="card-text">
                        <strong data-translate="modification_code">Modification Code:</strong> 
                        <span id="modCode" class="text-monospace"><?php 
                          if(isset($identifier)) {
                            $identifier2='joe'.$identifier; 
                            echo $year.'*'.$month.'*'.$week.'*'.crypt($identifier2,'ku');
                          } else {
                            echo 'N/A';
                          }
                        ?></span>
                        <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyToClipboard('modCode')">
                          <i class="fas fa-copy"></i>
                        </button>
                      </p>
                      <small class="text-muted" data-translate="security_note">Save these codes if you need to modify your file settings later</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card bg-light">
                    <div class="card-header">
                      <i class="fas fa-shield-alt mr-2"></i> <span data-translate="protections">Applied Protections</span>
                    </div>
                    <div class="card-body">
                      <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-light">
                          <i class="fas fa-calendar-times mr-2 text-danger"></i> 
                          <span data-translate="expires_on">Expires on:</span> <strong><?php 
                            if(isset($password)) {
                              echo date("F j, Y", strtotime("+$password day"));
                            } else {
                              echo 'N/A';
                            }
                          ?></strong>
                        </li>
                        <li class="list-group-item bg-light">
                          <i class="fas fa-eye-slash mr-2 text-danger"></i> 
                          <span data-translate="max_opens">Max opens:</span> <strong><?php echo isset($limit) ? $limit : 'N/A'; ?></strong>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="d-flex justify-content-center mb-4">
                <a class="btn btn-primary btn-xl mx-2" href="https://maipdf.com/enoffline.php" style="text-transform: none;" target="_blank">
                  <i class="fas fa-cog mr-2"></i> Manage File Settings
                </a>
                <a class="btn btn-success btn-xl mx-2" href="<?php echo $yanhuozip ?>" download>
                  <i class="fas fa-download mr-2"></i> Download Protected File
                </a>
              </div>
              
              <div class="text-center">
                <a class="btn btn-outline-danger" href="https://paypal.me/maitube" target="_blank">
                  <i class="fas fa-heart mr-2"></i> Support This Project
                </a>
              </div>
            </div>
          </div>
          
          <!-- How to use section -->
          <div class="accordion" id="howToUse">
            <div class="card">
              <div class="card-header" id="headingHow">
                <h2 class="mb-0">
                  <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseHow" aria-expanded="false" aria-controls="collapseHow">
                    <i class="fas fa-question-circle mr-2"></i> How to Use Your Protected File
                  </button>
                </h2>
              </div>
              <div id="collapseHow" class="collapse" aria-labelledby="headingHow" data-parent="#howToUse">
                <div class="card-body text-left">
                  <ol>
                    <li>Download the protected file (ZIP format)</li>
                    <li>Extract the ZIP file to your computer</li>
                    <li>Open the extracted HTML file with any modern web browser</li>
                    <li>Your PDF content will load with the applied protections</li>
                    <li>The file will stop working once any protection limit is reached</li>
                  </ol>
                  <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i> Works on Windows, Mac, and Android devices. Not compatible with iOS.
                  </div>
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </section>

<script>
function copyToClipboard(elementId) {
  const element = document.getElementById(elementId);
  const tempInput = document.createElement("input");
  tempInput.value = element.textContent;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);
  
  // Show copied notification
  const oldText = element.nextElementSibling.innerHTML;
  element.nextElementSibling.innerHTML = '<i class="fas fa-check"></i> Copied!';
  setTimeout(function() {
    element.nextElementSibling.innerHTML = oldText;
  }, 2000);
}
</script> 
  <!-- Footer -->
  <footer class="bg-light py-5">
    <div class="container">
      <div class="small text-center text-muted">Copyright &copy; 2026-joe@pdfhost.online</div>
      
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Plugin JavaScript -->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="vendor/magnific-popup/jquery.magnific-popup.min.js"></script>

  <!-- Custom scripts for this template -->
  <script src="js/creative.min.js"></script>

  <!-- Mobile-friendly animations -->
  <style>
    /* Enhance the user experience with smooth animations */
    .fade-in {
      animation: fadeIn 1s ease-in-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* Style the progress bar container */
    .progress-container {
      z-index: 1000;
    }
    
    /* Enhance dropzone appearance */
    .dropzone {
      transition: all 0.3s ease-in-out;
    }
    
    .dropzone:hover {
      transform: scale(1.02);
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    /* Add some pulse effect to the buttons */
    .btn-pulse {
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4); }
      70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
      100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
    }
    
    /* Make cards more appealing */
    .card {
      transition: all 0.3s ease;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    /* Improve mobile responsiveness */
    @media (max-width: 768px) {
      .btn-xl {
        padding: 0.75rem 1.25rem;
        font-size: 0.85rem;
      }
      
      h1 {
        font-size: 2rem;
      }
      
      .progress-container {
        top: 46px !important;
      }
    }
    
    /* Add animation to icons */
    .fa-lock, .fa-shield-alt {
      transition: transform 0.3s ease;
    }
    
    .fa-lock:hover, .fa-shield-alt:hover {
      transform: rotate(10deg);
    }
    
    /* Improve form fields */
    .form-control:focus {
      border-color: #9198e5;
      box-shadow: 0 0 0 0.2rem rgba(97, 102, 229, 0.25);
    }
  </style>
  
  <script>
    // Initialize tooltips
    $(function () {
      $('[data-toggle="tooltip"]').tooltip();
      
      // Add pulse effect to download button
      $('.btn-success').addClass('btn-pulse');
      
      // Apply fade-in animation to main sections
      $('.page-section').addClass('fade-in');
      
      // Add confirmation before leaving page with unsaved data
      $('form').on('change', function() {
        window.onbeforeunload = function() {
          return "You have unsaved changes. Are you sure you want to leave?";
        };
        
        $('form').on('submit', function() {
          window.onbeforeunload = null;
        });
      });
    });
  </script>

</body>

</html>
