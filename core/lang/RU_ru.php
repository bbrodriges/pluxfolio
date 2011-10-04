<?php

//часть установки
$INST_title = 'Установка Pluxfolio';
$INST_engine_version = 'Версия системы';
$INST_php_version = 'Версия PHP';
$INST_magicquotes_settings = 'Настройки "Magic quotes"';
$INST_gd_check = 'Библиотека GD';
$INST_default_lang = 'Язык по умолчанию';
$INST_creating_hero = 'Создание героя';
$INST_username = 'Логин администратора';
$INST_password = 'Пароль';
$INST_confirm_password = 'Подтверждение пароля';
$INST_send_notification = 'Послать уведомление разработчикам';

//часть админки
$ADM_auth_title = 'Авторизация';
$ADM_auth_legend = 'Администраторская панель';
$ADM_auth_enter = 'Войти';
$ADM_auth_backtosite = 'Вернуться на сайт';
$ADM_auth_loginerror = 'Неверный пароль';

$ADM_title = 'Администрирование';
$ADM_authorized_as = 'Вы вошли как';
$ADM_logout = 'Выйти';
$ADM_top_gallery_title = 'Галереи';
$ADM_top_postarticle_title = 'Чиркануть новость';
$ADM_top_articles_title = 'Новости';
$ADM_top_staticpages_title = 'Статичные страницы';
$ADM_top_articlescategories_title = 'Категории новостей';
$ADM_top_settings_title = 'Настройки';
$ADM_top_pictures_title = 'Картинки';
$ADM_top_files_title = 'Файлы';
$ADM_top_backtosite_title = 'Вернуться на сайт';

$ADM_yes = 'Да';
$ADM_no = 'Нет';
$ADM_savechanges = 'Сохранить изменения';
$ADM_page = 'Страница';
$ADM_of = 'из';
$ADM_poweredby_title = 'Работает на';
$ADM_week = 'Неделю';
$ADM_fifteendays = 'Пятнадцать дней';
$ADM_month = 'Месяц';
$ADM_example = 'Пример';
$ADM_imgorderby_name = 'По имени';
$ADM_imgorderby_date = 'По дате';
$ADM_update_available = 'Доступно обновление';
$ADM_upto_date = 'Последняя версия';

$SITE_maintence_message = '<h2>Сайт в разработке</h2> В данный момент сайт не доступен из-за проведения работ.<br/><br/>Только <a href="./core/admin/">администратор</a> может просматривать его.';
$SITE_maintence_label = '<span style="color:red;">Режим заглушки</span>';

$ADM_template_warning = 'Вы используете версию шаблона темы более старую, чем версия вашей системы. Это может привести к возникновению ошибок и потенциальных брешей в безопасности сайта.';

$ADM_nav_basesettings_title = 'Базовые настройки';
$ADM_nav_contentsettings_title = 'Настройки контента';
$ADM_nav_usersettings_title = 'Настройки пользователя';
$ADM_nav_adminsettings_title = 'Настройки админки';
$ADM_nav_sitepaths_title = 'Архитектура движка';
$ADM_nav_info_title = 'Информация';
$ADM_nav_themesettings_title = 'Редактор тем';
$ADM_nav_langsettings_title = 'Редактор языка';
$ADM_nav_analyticsettings_title = 'Аналитика';

$ADM_gal_title = 'Галереи — страницы с портфельными материалами';
$ADM_gal_galname = 'Заголовок';
$ADM_gal_isactive = 'Активна';
$ADM_gal_order = 'Порядок';
$ADM_gal_editgallery = 'Редактировать';
$ADM_gal_newgallery = 'Новая галерея';
$ADM_gal_isvisible = 'Видима';
$ADM_gal_link = 'Ссылка';

$ADM_gal_help = '<h3>Важное замечание </h3>
<p>У каждой галереи есть 2 параметра, <b>Название</b> и <b>URL</b>. Название задаёт текст ссылки в главном меню,  заголовок над контентом и тайтл страницы в коде, а URL — адрес (точнее alias) страницы  в браузере. При создании новой галереи в названии должна быть только латиница и/или цифры, при изменении — можно заменить латиницу на кириллицу. В поле URL кирилилца неприемлема.</p>
<h3>Алгоритм создания галерей простой:</h3>
<ol>
  <li>создаёте новую галерею с названием латиницей (и/или цифрами) без пробелов;</li>
  <li>идёте в раздел <b>Картинки</b>, выбираете в выпадающем списке вашу галерею (по параметру URL), кладёте в новую папку ваши работы (джипеги, гифы и пнг) оригинального размера (без превьюшек) — также можно делать тоже самое через FTP (папка /album/Ваша галерея;</li>
  <li>открываете в браузере ваш сайт, переходите на страницу новой  галереи — создаются превьюшки для ваших макетов, обновляется счётчик  ваших работ (в шапке) — на лету и очень автома<strong>г</strong>ически.</li>  
  <li>при желании меняете название галереи на кириллическое.</li>
</ol>
<h3>Чтобы изменить галерею</h3>
<p>просто нажмите Изменить галерею справа от параметров существующей галереи. Вы попадете на страницу изменения контентной части галереи — сопроводительного текста и графики для галереи. Сама галерея находится в папке /album/галерея/, названия картинок берутся из имён файлов, так что удаление файлов влечёт изменение самой галереи (её «галерейной» части). Все настройки для галерей находятся в папке шаблона сайта в файле galeria.php</p>
<h3>Чтобы удалить галерею</h3>
<p>просто очистите название существующей галереи и нажмите <b>Сохранить изменения</b></p>';

$ADM_galedit_title = 'Изменяем контент галереи';
$ADM_preview = 'Предпросмотр';
$ADM_galsourcecode = 'Исходный код галереи';
$ADM_galedit_help = '<h3>Какой язык программирования используется? </h3>
<p>Вы   можете использовать любой скриптовый язык или язык разметки (PHP, JavaScript, XHTML и т.д.) .. </p>
<h3>Об исходном коде</h3>
<p>Исходный код интерпретируется при загрузке страницы. Заголовки отправляются браузером в последнюю очередь. Поэтому можно играться с cookies, сессиями и т.д.</p>';

$ADM_galedit_title = 'Редактирование';
$ADM_preview = 'Предпросмотр';
$ADM_galsourcecode = 'Исходный код галереи';
$ADM_galedit_help = '<h3>Какой язык программирования я могу использовать?</h3><p>Вы можете использовать любой скриптовый язык или язык разметки (PHP, JavaScript, XHTML и т.д.)</p>';

$ADM_newarticle_title = 'Создание новости';
$ADM_postas = 'Написать как';
$ADM_newarticle_heading = 'Новая статья';
$ADM_newarticle_category = 'Категория';
$ADM_newarticle_actegories = 'Категории';
$ADM_newarticle_specialcategories = 'Специальные категории';
$ADM_newarticle_categoryactual = 'Актуальные';
$ADM_newarticle_categorydraft = 'Драфты';
$ADM_newarticle_pretexttitle = 'Затравка (необязательно)';
$ADM_newarticle_texttitle = 'Текст статьи (HTML уместны)';
$ADM_newarticle_datetitle = 'Дата';
$ADM_newarticle_settonow = 'Установить текущую';
$ADM_newarticle_externallink = 'Ссылка на сторонний сайт, если о нём шла речь в статье (необязательно)';
$ADM_newarticle_alias = 'Алиас новости (URL-имя) &mdash; <b>только латиницей</b>';
$ADM_newarticle_preview = 'Предпросмотр';
$ADM_newarticle_publish = 'Опубликовать';
$ADM_newarticle_delete = 'Удалить';

$ADM_articles_title = 'Список новостей';
$ADM_articles_listfilter_title = 'Фильтр списка';
$ADM_articles_listfilter_articletitle = 'Заголовок новости';
$ADM_articles_listfilter_incategory = 'в категории';
$ADM_articles_allarticles = 'Все новости';
$ADM_articles_listfilter_dofilter = 'Фильтр';
$ADM_articles_tabledate = 'Дата';
$ADM_articles_tabletitle = 'Заголовок';
$ADM_articles_tablecategory = 'Категория';
$ADM_articles_tableactions = 'Действия';
$ADM_articles_nonews = 'Пока статей нет';

$ADM_check_deletion = 'Вы уверены?';

$ADM_articleedit_title = 'Редактирование новости';
$ADM_articlepreview_title = 'Предпросмотр новости';

$ADM_staticpages_title = 'Создание и редактирование статических страниц';
$ADM_staticpages_newstaticpage = 'Новая страница';
$ADM_staticpages_help = '<h3>Создание страниц</h3>
	  <p>Чтобы создать новую страницу впишите название в пустое поле напротив ID «Новая страница». <br />
	  <br />
	  <b>Важное замечание</b>: при создании в названии может быть только латиница, при редактировании можно заменить название на кириллическое, а URL (точнее alias) всегда должен содержать только латиницу и цифры.<br />
	  <br />
	  На сайте отображаются страницы только в активном состоянии</p>
	  <p>После того, как cтраница создана, вы можете изменить её исходный код, нажав на кнопку «Изменить страницу».  </p>
	  <h3>Изменение страницы</h3>
	  <p>Просто   отредактируйте название и URL и нажмите кнопку «Сохранить изменения».</p>
	  <h3>Удаление страниц</h3>
	  <p>Просто оставьте пустым поле с названием страницы. </p>';

$ADM_staticedit_title = 'Редактирование страницы';
$ADM_pagesourcecode = 'Исходный код страницы';

$ADM_categories_title = 'Создание и редактирование категорий';
$ADM_category_title = 'Название категории';
$ADM_category_sorttitle = 'Сортировка';
$ADM_sortby_desc = 'по убыванию';
$ADM_sortby_asc = 'по возрастанию';
$ADM_category_artperpage = 'Статей на страницу';
$ADM_category_newcategory = 'Новая категория';
$ADM_category_help = '<h3>Создание категорий </h3>
	  <p>Чтобы создать новую категорию впишите название в пустое поле напротив ID «Новая категория». <br />
	  <br />
	  <b>Важное замечание</b>: при создании в названии может быть только латиница, при редактировании можно заменить название на кириллическое, а URL (точнее alias) всегда должен содержать только латиницу и цифры.<br />
	  <h3>Изменение категорий</h3>
	  <p>Просто отредактируйте название и URL и нажмите кнопку «Сохранить изменения».</p>
	  <h3>Удаление категорий</h3>
	  <p>Просто оставьте пустым поле с названием категории. </p>';

$ADM_basesettings_title = 'Изменение базовых настроек';
$ADM_basesettings_legend = 'Базовые настройки';
$ADM_basesettings_sitetitle = 'Название сайта';
$ADM_basesettings_sitedescription = 'Описание сайта';
$ADM_basesettings_sitedefinition = 'Краткое определение сайта';
$ADM_basesettings_sitekeywordstag = 'Содержимое мета-тэга keywords';
$ADM_basesettings_sitedescriptiontag = 'Содержимое мета-тэга description';
$ADM_basesettings_sitelanguage = 'Язык сайта';
$ADM_basesettings_maintence = 'Включить режим заглушки';
$ADM_basesettings_sitealias = 'Адрес сайта (пример http://mysite.ru/portfolio)';
$ADM_basesettings_sitetimezone = 'Часовой пояс (по Гринвичу)';

$ADM_contentsettings_title = 'Изменение настроек контента';
$ADM_contentsettings_legend = 'Вывод контента';
$ADM_worksnthumbs_legend = 'Работы и превью';
$ADM_watermark_legend = 'Настройки водяных знаков';
$ADM_contentsettings_displaynews = 'Включить блог';
$ADM_contentsettings_frontdisplay = 'Выводить на главной';
$ADM_contentsettings_sitetemplate = 'Шаблон сайта';
$ADM_contentsettings_articlesort = 'Сортировать новости';
$ADM_contentsettings_catlistcollapse = 'Показывать список категорий';
$ADM_contentsettings_catliststatic = 'Статичным';
$ADM_contentsettings_catlistcollapsable = 'Скрываемым';
$ADM_contentsettings_displaycounter = 'Отображать счетчик работ';
$ADM_contentsettings_enableimgsets = 'Объединять картинки в наборы';
$ADM_contentsettings_enablefreshness = 'Показывать значек "свежести" картинок';
$ADM_contentsettings_freshnesstime = 'Время свежести картинки';
$ADM_twitter_translation = 'Включить трансляцию твиттов';
$ADM_twitter_translationhelp = 'Введите имя вашего аккаунта в твиттере, чтобы включить трансляцию';
$ADM_images_caption = 'Отображать название работ';
$ADM_images_order = 'Порядок отображения работ'; 
$ADM_contentsettings_articlesperpage = 'Новостей на страницу';
$ADM_contentsettings_articlesperadminpage = 'Кол-во новостей на страницу в админке';
$ADM_contentsettings_articlesperatom = 'Кол-во новостей в Атоме';
$ADM_contentsettings_thumbssizes = 'Размеры миниатюр';
$ADM_thumbtype_title = 'Отображать миниатюры';
$ADM_thumbtype_scale = 'Уменьшенными';
$ADM_thumbtype_crop = 'Вырезанными';
$ADM_contentsettings_thumbwidth = 'Ширина';
$ADM_contentsettings_thumbheight = 'Высота';
$ADM_contentsettings_thumboffesthelp = '<a href="remove_tb.php">Удалите</a> все файлы с расширением <b>.tb</b> после изменения';
$ADM_watermark_enable = 'Добавлять водяные знаки';
$ADM_watermark_text = 'Текст водяного знака';
$ADM_contentsettings_watermarkhelp = 'Водяной знак не может быть убран после создания. <a href="remove_tb.php">Удалите</a> все файлы с расширением <b>.tb</b>, чтобы пометить старые файлы. Замените файл <b>font.ttf</b> в директории /js вашим, чтобы изменить шрифт водяного знака.<br><b>Некоторые версии GD не поддерживают не латинский текст в водяных знаках.</b>';

$ADM_usersettings_title = 'Редактирование данных пользователя';
$ADM_usersettings_legend = 'Администратор сайта';
$ADM_usersettings_login = 'Логин';
$ADM_usersettings_password = 'Пароль';
$ADM_usersettings_confirmpassword = 'Подтверждение пароля';

$ADM_adminsettings_title = 'Редактирование настроек админки';
$ADM_adminsettings_legend = 'Настройки админки';
$ADM_adminsettings_admintype = 'Тип вывода админки';
$ADM_adminsettings_extended = 'Расширенный';
$ADM_adminsettings_simple = 'Упрощенный';
$ADM_adminsettings_wysiwyg = 'Показывать кнопки автотэгов рядом с полями текста';
$ADM_adminsettings_postas = 'Показывать поле "Написать от имени" при создании новости';
$ADM_adminsettings_templatecheck = 'Проверять совместимость шаблона и системы';

$ADM_themesettings_title = 'Редактировать текущую тему';
$ADM_themesettings_currenttheme = 'Текущая тема';
$ADM_themesettings_editingfile = 'Сейчас редактируем';
$ADM_themesettings_fileslist = 'Доступные файлы';

$ADM_langsettings_title = 'Редактирование языкового пакета';
$ADM_langsettings_currentlang = 'Текущий языковой пакет';

$ADM_analyticsettings_title= 'Настройки аналитики';
$ADM_googleanalytics_title = 'Код счетчика Google Analytics';
$ADM_googleanalytics_help = 'Вставьте код счетчика GA';
$ADM_yandexmetrika_title = 'Код счетчика Яндекс.Метрики';
$ADM_yandexmetrika_help = 'Вставьте код счетчика ЯМ';

$ADM_pathssettings_title = 'Архитектура движка';
$ADM_pathssettings_legend = 'Настройка системных путей к файлам и папкам';
$ADM_pathssettings_picturesfolder = 'Папка с изображениями';
$ADM_pathssettings_filesfolder = 'Папка с файлами';
$ADM_pathssettings_articlesfolder = 'Папка для хранения новостей';
$ADM_pathssettings_staticsfolder = 'Папка для хранения статичных страниц';
$ADM_pathssettings_categoriesfile = 'Категории новостей храняться в файле';
$ADM_pathssettings_statictitlesfile = 'Заголовки страниц сайта храняться в файле';
$ADM_pathssettings_loginpassword = 'Логины и пароли храняться в файле';

$ADM_info_title = 'Статистика по сайту';
$ADM_info_description = 'В случае каких-либо проблем данный раздел может подсказать пути решения.';
$ADM_info_version = 'Версия движка';
$ADM_info_encoding = 'кодировка';
$ADM_info_catnumber = 'Кол-во категорий';
$ADM_info_staticnumber = 'Кол-во статичных страниц';
$ADM_info_loginas = 'Вы вошли как';
$ADM_info_phpversion = 'Ваша версия PHP';
$ADM_info_magicquotes = 'Настройки "Magic quotes"';

$ADM_images_title = 'Загрузка картинок на сайт';
$ADM_images_help = 'Кстати, если выбрать папку с картинками и нажать "Поехали!" &mdash; выведется список всех картинок в этой папке.';
$ADM_images_legend = 'Загрузка картинок';
$ADM_images_intofolder = 'В папку';
$ADM_images_defaultfolder = 'Папка по умолчанию';
$ADM_images_go = 'Поехали!';
$ADM_images_multiuploadnotice = 'Многофайловая загрузка использует механизмы HTML5. Не все современные браузеры поддерживают данную функцию.';
$ADM_youtube_legend = 'Загрузка YouTube-ролика';
$ADM_youtube_uploadnotice = 'Введите ID ролика в поле выше (пример nVxnNNGjubg)';
$ADM_images_previouslyuploaded = 'Ранее загруженые картинки';
$ADM_images_noimages = 'Нет загруженых картинок';

$ADM_imagecrop_title = 'Создание миниатюры';
$ADM_imagecrop_help = 'Нажмите на изображение и потяните, чтобы создать область выделения. Сбросьте кэш вашего браузера после создания миниатюры, чтобы увидеть изменения.';

$ADM_imagerename_title = 'Переименование работы';
$ADM_imagerename_filename = 'Название работы';
$ADM_imagerename_filedescription = 'Описание';
$ADM_imagerename_filetag = 'Тэг (для объединения в набор)';
$ADM_imagerename_help = 'Не задавайте слишком длинное описание или имя файла. Это может привести к ошибкам в ОС Windows.';

$ADM_documents_title = 'Загрузка файлов на сайт';
$ADM_documents_legend = 'Форма загрузки';
$ADM_documents_upload = 'Загрузить';
$ADM_documents_uploaded = 'Ранее загруженые файлы';
$ADM_documents_filename = 'Имя файла';
$ADM_documents_nofiles = 'Нет файлов';

//часть сайта
$SITE_headermenu_firstmain = 'Главная';
$SITE_headermenu_firstnews = 'Новости';

$SITE_postedon = 'Написано';
$SITE_category = 'в категории';
$SITE_article_more = 'дальше...';

$SITE_twitts_title = 'Мои твиты';
$SITE_twitts_mytwitter = 'Мой твиттер';

$SITE_categories_title = 'Категории';
$SITE_atomfeed = 'Новостной Атом';

$SITE_404_title = 'Ошибка 404';
$SITE_404_message = '<p>Страница не найдена.</p><p>Вернуться на <a href="./">главную</a>.</p>';

$SITE_footer = '<p><a href="core/admin/">Админка</a> | Движок: <a href="http://pluxfolio.ru/">pluxfolio</a>, шаблон: ';

$SITE_month_january = 'января';
$SITE_month_february = 'февраля';
$SITE_month_march = 'марта';
$SITE_month_april = 'апреля';
$SITE_month_may = 'мая';
$SITE_month_june = 'июня';
$SITE_month_july = 'июля';
$SITE_month_august = 'августа';
$SITE_month_september = 'сентября';
$SITE_month_october = 'октября';
$SITE_month_november = 'ноября';
$SITE_month_december = 'декабря';

$SITE_day_monday = 'Понедельник';
$SITE_day_tuesday = 'Вторник';
$SITE_day_wednesday = 'Среда';
$SITE_day_thursday = 'Четверг';
$SITE_day_friday = 'Пятница';
$SITE_day_saturday = 'Суббота';
$SITE_day_sunday = 'Воскресение';

$SITE_counter_pretext = 'Кстати, уже';
$SITE_counter_aftertext = 'работы в галереях.';

?>
