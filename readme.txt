=== StopWords ===
Contributors: Obelchenko
Tags: stopwords, stop words, stop, words, spam, comments, spam, antispam, anti-spam, anti spam, post moderation, comment moderation, post spam, comment spam, spam comments, spam posts
Requires at least: 4.0
Tested up to: 5.0.0
Stable tag: 1.0
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://donate.obelchenko.ru

Stop Words фильтрует новый или редактируемый пост и отказывает в публикации при:
* при нахождении заперщенных слов; 
* при слишком коротком или длинном заголовке; 
* при слишком коротком или длинном тексте поста; 
* при низком качестве текста; 

*Machine translation:*

Stop Words filters a new or edited post and refuses to publish when:
* when finding locked words;
* if the title is too short or long;
* if the post is too short or long;
* with low quality text;

== Description ==

Не секрет, что среди пользователей находится масса ублюдков, которые бессовестно засирают сайты своим спамом. 
Например, казино, виагра, сетевой маркетинг и т.п.
Зачастую они пишут только заголовок и кучу хэш-тэгов или просто набор ключевых слов и ссылок вместо постов.
Чтобы запретить проскальзывание на паблик такого отстоя сделан этот плагин.
Доверенные пользователи сайта могут быть исключены из обработки постов плагином и писать все, что им захочется :)

*Machine translation:*

It is no secret that among the users there are a lot of bastards who unscrupulously foul up websites with their spam.
For example, casino, viagra, network marketing, etc.
Often they write only the title and a bunch of hash tags or just a set of keywords and links instead of posts.
To prevent slippage on the public of such sludge made this plugin.
Trusted users of the site can be excluded from the processing of posts by the plugin and write whatever they want :)

== Installation ==

*Русский:*

Внимание! Для корректой работы плагина требуется плагин Classic Editor!

1. Перейдите на экран добавления новых плагинов в админке WordPress.
2. Найдите «Stop Words»
3. Нажмите «Установить сейчас» и активируйте плагин

Для ручной установки через FTP:

1. Загрузите папку stop-words в каталог `/ wp-content / plugins /`
2. Активируйте плагин через экран «Плагины» в вашей админ-панели WordPress.

Чтобы загрузить плагин через WordPress вместо FTP:

1. Загрузите zip-файл на экране «Добавить новые» (см. Вкладку «Загрузка») в админке WordPress и активируйте.

*English:*

Attention! To work correctly, the plugin requires the Classic Editor plugin!

For an automatic installation through WordPress:

1. Go to the 'Add New' plugins screen in your WordPress admin area
2. Search for 'Stop Words'
3. Click 'Install Now' and activate the plugin

For a manual installation via FTP:

1. Upload the addthis folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' screen in your WordPress admin area

To upload the plugin through WordPress, instead of FTP:

1. Upload the downloaded zip file on the 'Add New' plugins screen (see the 'Upload' tab) in your WordPress admin area and activate.

== Frequently Asked Questions ==

= Я хочу отблагодарить автора =

Если плагин спас Ваш сайт от набега спаммеров и Вы испытываете благодарность за это, 
то Вы можете перечислить любую сумму в качестве вознаграждения автору здесь https://donate.obelchenko.ru.

= Stop Words бесплатен? =

Да, бесплатен. 

= Нужна ли учетная запись для использования плагина? =

Нет. Плагин работает без использования каких-то личных данных.

= Что проверяется плагином? =

В настройках плагина вводятся правила фильтрации в виде "*слово*", "!слово*", "!*слово", "*сл*во*" и пр.
При публикации поста или комментария в админке или с паблика плагин проверяет введенный текст на соответствие правилам.
Если совпадения обнаружены в комментарии, то он отправляется в спам.
Если совпадения обнаруживаются при публикации поста, то пост сохраняется в черновиках.

= Плагин может фильтровать url? =

Да. Если необходимо запретить использование ссылок на некоторые ресурсы, то в правилах фильтрации достаточно добавить правило, в котором указан домен. 
Например: !*site.com*, *site.com* или *site.*

= Регулируется ли строгость проверки? =

Для того, чтобы правила срабатывали с разной степенью строгости, используется параметр настройки "количество совпадений". 
Если его установить в 2, то запрет на публикацию сработает только при наличии 2 или более сработавших правил.
При 3 - от трех совпадений и т.д.

= Что значит ! перед правилом фильтрации? =

Если перед сработавшим правилом стоит !, то запрет срабатывает вне зависимости от значения параметра "количество совпадений".

*Machine translation:*

= I want to thank the author =

If the plugin saved your site from a spammer raid and you are grateful for it,
then you can transfer any amount as a fee to the author here https://donate.obelchenko.ru.

= Stop Words free? =

Yes, it's free.

= Do I need an account to use the plugin? =

No. The plugin works without using any personal data.

= What is checked by the plugin? =

In the plugin settings, filter rules are entered in the form of "*word*", "!Word*", "!*Word", "*w*rd*", etc.
When publishing a post or comment in the admin panel or from the public, the plugin checks the entered text for compliance with the rules.
If a match is found in a comment, it is sent to spam.
If matches are found when posting, the post is saved in draft.

= Can plugin filter url? =

Yes. If it is necessary to prohibit the use of links to some resources, then in the filtering rules it is enough to add a rule in which the domain is specified.
For example: !*site.com*, *site.com* or *site.*

= Is the test strict? =

In order for the rules to be triggered with varying degrees of rigor, the number of matches setting is used.
If it is set to 2, then the ban on publication will work only if there are 2 or more rules that have worked.
With 3 - from three matches, etc.

= What does it mean ! before filtering rule? =

If a rule is preceded by a "!", then a ban is triggered regardless of the value of the parameter "number of matches"

== Screenshots ==

1. Настройка плагина / Plugin setup

== Changelog ==

= 1.0 =
*Первая версия - 1 July 2019*

== Upgrade Notice ==

= 1.0 =
Обновлений нет
