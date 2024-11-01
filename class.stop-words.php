<?php

class CStopWords 
{
    protected static $instance;
    private static $msg;
    
    public static function init()
    {                      
        is_null( self::$instance ) AND self::$instance = new self;
        
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'my_register_plugin_styles') );
        
        add_filter( 'plugin_action_links_' . STOPWORDS_PLUGIN_BASENAME, array( __CLASS__,'my_settings_link'));
        add_action( 'admin_menu', array( __CLASS__, 'my_admin_menu' ));
        add_action( 'admin_bar_menu', array( __CLASS__, 'my_toolbar_link' ) , 10000 );
        
        add_action( 'save_post', array( __CLASS__, 'my_save_post'), 10, 2 );
        add_action( 'edit_post', array( __CLASS__, 'my_save_post'), 10, 2 );
        add_action( 'admin_notices', array( __CLASS__, 'my_admin_notices') );                        
        add_action( 'wp_insert_comment', array( __CLASS__, 'my_wp_insert_comment') );
        
        return self::$instance;
    }

    // Регистрируем файл стилей и добавляем его в очередь
    static function my_register_plugin_styles() {    
        wp_register_style( STOPWORDS_PLUGIN_BASENAME, plugin_dir_url(__FILE__)."stop-words.css" );
        wp_enqueue_style( STOPWORDS_PLUGIN_BASENAME );
    }
    
    // Ссылка из верхнего тулбара
    static function my_toolbar_link( $wp_admin_bar ) {
            $args = [
                    'id'    => strtolower(__CLASS__).'-link',
                    'title' => '<span class="ab-icon"><img src='.STOPWORDS_ICON_URL.'></span>'.STOPWORDS_NAME,
                    'href'  =>  get_site_url() . '/wp-admin/admin.php?page=stopwords',
                    'meta'  => [
                            
                            'title' => STOPWORDS_NAME
                    ]
            ];
            $wp_admin_bar->add_node( $args );
    }

    // Ссылка Settings на странице плагинов
    static function my_settings_link($links) 
    {
        $settings_link = '<a href="admin.php?page=stopwords">' . __('Settings', 'stopwords') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }	

    // Ссылка на плагин в левом меню
    static function my_admin_menu() {
        add_menu_page( STOPWORDS_NAME, STOPWORDS_NAME, 'activate_plugins', 'stopwords', array(__CLASS__, 'plugin_options'), STOPWORDS_ICON_URL);
    }

    // Умолчания при активации
    static function on_activation() 
    {
        add_option('stopwords_exclude_users', ""); 
        add_option('stopwords_rules', 
            [
                "deadline"=>2,
                "deadlength"=>0,
                "deadtitlength"=>0,
                "words"=>["!http://*asino.*","*азино*","*asino*"],
            ]
        );
    }
	
    // Удаление данных при удалении плагина
    static function on_uninstall () {
        delete_option('stopwords_exclude_users');
        delete_option('stopwords_rules');
    }
				
    
    // Страница админки
    static function plugin_options() {
            if (!current_user_can('manage_options'))  {
                    wp_die( __('You do not have sufficient permissions to access this page.') );
            } else {                 
                    if ( isset($_POST["save"])  )
                    { 
                            check_admin_referer( 'check_nonce', 'check_nonce' );
                            // доверенные пользователи
                            $users = explode(" ", @$_POST["ids"]);
                            $ids = "";
                            foreach ($users as $id) {
                                    $ids .= ceil(doubleval(trim($id)))." ";
                            }
                             update_option( "stopwords_exclude_users", $ids);

                            // настройки стоп-слов в тексте и ссылках 
                            $rules = [
                                "deadline"=>0+intval(@$_POST["rule_deadline"]),
                                "deadlength"=>0+intval(@$_POST["rule_deadlength"]),
                                "deadtitlength"=>0+intval(@$_POST["rule_deadtitlength"]),
                                "words"=>array_unique(explode("\n", sanitize_textarea_field(@$_POST["rule_words"]))),
                            ];
                            
                            update_option( "stopwords_rules", $rules); 
                    };	

            $classic_editor = is_plugin_active( 'classic-editor/classic-editor.php' ) ? "" : "<p style='color:red'><b>Для корректной работы плагина необходим активный плагин Classic Editor !!!</b></p>";
?>    
    <form method="post" action="">
	<div class="wrap">
            <img src="<?=STOPWORDS_IMG_URL?>" class="stopwords-img">
            <div class="stopwords-info">
                <h2 class="stopwords-h2"><?=STOPWORDS_NAME?></h2>
                <p>
                    Плагин проверяет наличие в посте запрещенных слов или ссылок на внешние ресурсы.
                    <br>
                    Если что-то из запрещенного обнаружено, то пост не опубликуется, а комментарий отправляется в спам.
                    <br>
                </p>
            </div>    
            <div class="stopwords-clear"></div>

            <?=$classic_editor?>
            
	    <?php if ( isset($_POST["save"]) && ($_POST["save"] == true) ){ ?>
	        <div class="updated below-h2" id="message">
	            <? echo __('Settings saved','stopwords');  ?>
	        </div>        
	    <?php }; // endif ?> 
            
        <?
            $rules = get_option("stopwords_rules");
            $deadline = 0+intval(@$rules["deadline"]);
            $deadlength = 0+intval(@$rules["deadlength"]);
            $deadtitlength = 0+intval(@$rules["deadtitlength"]);
            $words = sanitize_textarea_field(implode("\n", @$rules["words"]));
        ?>            
        <p class="submit">
            <b>Укажите через пробел id доверенных пользователей, для которых игнорировать правила:</b><br>
	    <input type="text" name=ids style="width:200px" value="<?=trim(get_option("stopwords_exclude_users"))?>"> (Ваш id: <b><?=get_current_user_id()?></b>)
            <br>            
            <br>            
            <b>Список правил - шаблонов вида "porno", "!porno", "!*азино", "казин*", "*ul*an" для поиска в тексте.</b>
            <br> 
            По одному правилу на каждой строчке:
            <br>
            <textarea name="rule_words" style="width:400px" rows="10"><?=$words?></textarea>
            <br>            
            <br>            
            <b>Укажите количество совпадений для отказа в публикации поста (по умолчанию - 2):</b><br>
            <input type="number" name=rule_deadline style="width:200px" value="<?=$deadline?>">
            <br>            
            <br>            
            <b>Укажите максимальную длину заголовка поста без пробелов (по умолчанию - 0, отключено):</b><br>
            <input type="number" name=rule_deadtitlength style="width:200px" value="<?=$deadtitlength?>">
            <br>            
            <br>            
            <b>Укажите минимальную длину поста без пробелов (по умолчанию - 0, отключено):</b><br>
            <input type="number" name=rule_deadlength style="width:200px" value="<?=$deadlength?>">
            <br>            
            <br>            
            <b>Символ *</b> заменяет произвольное количество непробельных символов, либо их отсутствие.            
            <br>
            <br>
            <b>Символ !</b> перед правилом означает, что параметр "количество совпадений" при совпадении игнорируется и пост не публикуется.             
            <br>
            <br>
            <b>Примеры правил:</b>
            <br>            
            Правило "porno" найдет слово "porno"<br>            
            Правило "*азино" найдет слова "азино", "казино", "рассказино"<br>            
            Правило "казин*" найдет слова "казин", "казино", "казиношка"<br>            
            Правило "*ul*an*" найдет слова "vulkan", "vulcan", "vulckan", "pulsan", "vulkanization"<br>
            Правило "!*casino.*" найдет url "http://casino.ru", "https://dorcasino.site", "http://test.mycasino.com" и т.д.<br>
        </p>
        <p>
            <input type="submit" name="save" id="submit" class="button button-primary" value="<?php echo __('Save Changes','stopwords');?>">
            <?php wp_nonce_field('check_nonce','check_nonce'); ?> 
        </p>	
	</div>
    </form>		
	<?php 
            }
    }


    /**
     * Хуки и вспомогательные функции
     */

    // Проверка наличия юзера в списке исключений
    private static function loyalUser() 
    {
            $ids = get_option( "stopwords_exclude_users");
            $ids = explode(" ", $ids);
            return in_array(get_current_user_id(), $ids);
    }
    
    // Замена спецсимволов в правиле
    private static function escape($s)
    {
        $nosp = "[^\s]*";
        
        $patterns = array();
        $replacements = array();
        
        $patterns[0] = '/\./';  $replacements[0] = "\.";
        $patterns[1] = '/\//';  $replacements[1] = "\/";
        $patterns[2] = '/\:/';  $replacements[2] = "\:";
        $patterns[3] = '/\*/';  $replacements[3] = $nosp;
        
        return "" . preg_replace($patterns, $replacements, $s);        
    }

    // Проверка статистики текста
    private static function checkStat($check_string)
    {
        // настройки статистики
        $min_sentences_count = 5;
        $min_sentence_words_count = 4;
        $max_sentence_symbols_count = 5;
        $min_stat = 3.5;
        
        $check_string = preg_replace('/<a.*?\/a>/imsu','',$check_string); //удаляем ссылки вместе с анкором 
        $check_string = strip_tags($check_string);
        $check_string = preg_replace('/http\S*?\s/imsu','',$check_string); //удаляем ссылки, если они не в <a ...>        
        $check_string = preg_replace('/\#\S+?\s/imsu','',$check_string); //удаляем хэш-тэги
        $check_string = preg_replace("/&nbsp;+/imsu"," ",$check_string);
        $check_string = preg_replace("/[\s]+/imsu"," ",$check_string);
        
        //заменим концы предложений и др. "слово .Новое предложение" на "слово. новое предложение"
        $check_string = preg_replace("/[\s](\.|\,|\!|\?)+?/imsu","$1 ",$check_string);

        //заменим многоточия на точки
        $check_string = preg_replace("/[\.]{3,}/imsu",".",$check_string);
//var_dump($check_string);        
        //заменим вещественные числа и даты на заглушку
        $check_string = preg_replace("/([\.\,][\d]+)+/imsu","dummy",$check_string);
        
        // Сначала ищем предложения
        $sentences = preg_split("/[\.\!\?]+/", $check_string, 0, PREG_SPLIT_NO_EMPTY);        
        $sentences_count = sizeof($sentences);

        // Нет смысла читать дальше, если предложений мало.
//var_dump($sentences_count);die;
        if($sentences_count < $min_sentences_count) return false;

        $total_words = 0;
        $total_symbols = 0;
        $short_sentences_count = 0;
        $mass_symbols_count = 0;
        // разбиваем предложения на слова и считаем оценочные параметры
        foreach($sentences as $sent)
        {
            // Разбиваем предложение на слова
            $words = preg_split('/[^a-zA-Z0-9А-Яа-яёЁ+]/u', $sent, 0, PREG_SPLIT_NO_EMPTY);
            $words_count = sizeof($words);
            if($words_count < $min_sentence_words_count) $short_sentences_count++;

            // Разбиваем предложение на символы
            $symbols_count = sizeof(preg_split("/[\,\:\;]+/", $sent, 0, PREG_SPLIT_NO_EMPTY));
            $words_by_symbols = $words_count / $symbols_count;
            $mass = "0";
            $sention = "";
            if($symbols_count > $max_sentence_symbols_count) 
            {
                $mass_symbols_count++;
                $mass = 1;
                $sention = $sent;
            }
            
            $total_words += $words_count;
            $total_symbols += $symbols_count;
            print("слов: $words_count,\tсимв: $symbols_count,\tмасс: $mass,\tсоотн: $words_by_symbols\t$sention\n");
        }
        
        print("\n");
        print("Всего слов: $total_words, симв: $total_symbols\n");
        print("Среднее слов: ".$total_words/$sentences_count.", симв: ". $total_symbols/$sentences_count. "\n");
        print("Коротких: $short_sentences_count, длинных: ".($sentences_count-$short_sentences_count).", соотн:".($short_sentences_count/$sentences_count)."\n");
        print("Многосимв: $short_sentences_count, длинных: ".($sentences_count-$short_sentences_count).", соотн:".($short_sentences_count/$sentences_count)."\n");
        
        $stat = $total_words / $total_symbols;        
        print("Стат: $stat\n");                
        
//die();        
        return ($min_stat < $stat);
    }    
    
    // Проверка совпадения строки с правилами
    private static function checkWords($check_string)
    {
        $sp = "[\s]+?";
        $rules = get_option("stopwords_rules"); 
        $deadline = 0+intval(@$rules["deadline"]);
        $words = @$rules["words"];        
        $found = [];                    
        
        foreach($words as $word)
        {
            $word = trim($word);
            if(empty($word)) continue;
            $important = $word[0] == "!";

            if($important) $word = trim(mb_substr($word, 1));
            
            $mask = self::escape($word);
            $mask = $sp . "(" . $mask . ")" . $sp;
//var_dump($mask);            
            $exec = preg_match_all('/'.$mask.'/imsu', $check_string, $matches);
//var_dump($exec);            
//var_dump($matches);
            if(($exec !== false) && ($exec>0))
            {
              for($i=0; $i<$exec; $i++)  
                if(isset($matches[1][$i]))
                    $found[] = strip_tags($matches[1][$i]); 
              if($important) return $found;
            }
        }                        
//var_dump($found);
//die;
        if($deadline > sizeof($found)) return false;        
        return (sizeof($found)>0) ? $found : false;
    }
    
    // Проверка длины поста
    private static function checkLength($deadlength, $check_string, $oper="!=")
    {                
        // если 0, значит отключена проверка длины, возвращаем успех
        if($deadlength==0) return true; 
        
        $check_string = preg_replace('/<a.*?\/a>/imsu','',$check_string); //удаляем ссылки вместе с анкором 
        $check_string = strip_tags($check_string);
        $check_string = preg_replace('/http\S*?\s/imsu','',$check_string); //удаляем ссылки, если они не в <a ...>        
        $check_string = preg_replace('/\#\S+?\s/imsu','',$check_string); //удаляем хэш-тэги
        $check_string = preg_replace("/[\s]+/imsu","",$check_string);
//if($oper==">") die(var_dump($check_string));
        // возвращаем успех, если длина соответствует параметру проверки
        switch($oper)
        {
            case ">": return mb_strlen($check_string) > $deadlength;
            case ">=": return mb_strlen($check_string) >= $deadlength;
            case "<": return mb_strlen($check_string) < $deadlength;
            case "<=": return mb_strlen($check_string) <= $deadlength;
            case "=": return mb_strlen($check_string) == $deadlength;
            default: return mb_strlen($check_string) != $deadlength;
        }        
    }
    
    // Проверка поста на правила
    public static function my_save_post( $post_id, $post)
    {        
        // Если пользователь из списка исключаемых, то действуем по умолчаниям
        if(self::loyalUser()) return;
                
        // Получим реальный ID поста, если это ревизия
        if ( $parent_id = wp_is_post_revision( $post_id ) ) $post_id = $parent_id;
        
        // Ловим только момент публикации поста
        if($post->post_status != "publish") return;        
        
        $uid = get_current_user_id();        
        $rules = get_option("stopwords_rules"); 
        $deadlength = 0+intval(@$rules["deadlength"]);
        $deadtitlength = 0+intval(@$rules["deadtitlength"]);
        $found = false;
                        
        // Проверяем длину заголовка на превышение
        if(!self::checkLength($deadtitlength, $post->post_title, "<")) 
        {
            self::$msg =<<<MSG
Пощадите!!!<br>
Для какого гения Вы сформулировали такой длинный заголовок!<br>
На этом сайте попрошу использовать лаконичные заголовки длиной хотя бы до {$deadtitlength} симолов!!!<br>Пробелы можете не считать :)<br>
Статус записи: <b>черновик!</b><br>
Если Вы жаждите всё же опубликовать данный материал, то потрудитесь изменить в настройках плагина параметр «Максимальная длина заголовка»!
MSG;
            $found = true;            
        }
        
        // Проверяем длину заголовка на пустоту
        if(mb_strlen(trim($post->post_title))<20)
        {
            self::$msg =<<<MSG
О, нет!!!<br>
Не пойму, что с заголовком!<br>
Используйте хотя бы 20 - 25 симолов!!!<br>Пробелы можете не считать :)<br>
Статус записи: <b>черновик!</b>
MSG;
            $found = true;            
        }
        
        // Проверяем длину поста
        if(!$found && !self::checkLength($deadlength, $post->post_content, ">"))
        {
            self::$msg =<<<MSG
Я в недоумении!<br>
Вы практически ничего не сказали!<br>
На этом сайте попрошу писать содержательные посты хотя бы из {$deadlength} симолов!!!<br>Пробелы можете не считать :)<br>
Статус записи: <b>черновик!</b><br>
Если Вы жаждите всё же опубликовать данный материал, то потрудитесь изменить в настройках плагина параметр «Минимальная длина поста»!
MSG;
            $found = true;            
        }
        
        if(!$found)
        {            
            $founded_words = self::checkWords($post->post_title . " " . $post->post_content . " " . $post->post_excerpt);
            if($founded_words !== false)
            {
                $founded = "«".implode("», «", $founded_words)."»";
                self::$msg =<<<MSG
Мне не послышалось?<br>
Вы сказали {$founded}?<br>
На этом сайте попрошу не выражаться!!!<br>
Употребление подобных выражений здесь запрещено!<br>
Статус записи: <b>черновик!</b><br>
Если Вы жаждите всё же опубликовать данный материал, то потрудитесь добавить свой id пользователя ($uid) в исключения в настройках плагина {$stopwords_name} или измените правила!
MSG;
            }            
            $found = !empty($founded_words); 
        }
        
        if(!$found)
        {            
            $check_stat = self::checkStat($post->post_content);
            if(!$check_stat)
            {                
                self::$msg =<<<MSG
О, нет! Никогда такого не было и вот опять!<br>
На этом сайте допускаются только читабельные и качественные тексты!!!<br>
Ваш текст под это описание не подходит!<br>
Статус записи: <b>черновик!</b><br>
Если Вы жаждите всё же опубликовать данный материал, то потрудитесь еще поработать над текстом, чтобы его читал не Яндекс, а наши читатели!
MSG;
            }            
            $found = !$check_stat; 
        }
        
        $status = $found ? 'draft' : $post->post_status;

        // если все условия прошли, то ничего не делаем
        if(!$found) return;        
        
        // Удаляем хук, чтобы не было зацикливания
        remove_action( 'edit_post', array( __CLASS__, 'my_save_post'), 10, 3 );
        remove_action( 'save_post', array( __CLASS__, 'my_save_post'), 10, 3 );
        
        // обновляем запись. В это время снова срабатывает событие save_post!!
        wp_update_post( array( 'ID' => $post_id, 'post_status'=> $status ));
        
        // Ставим хук обратно
        add_action( 'edit_post', array( __CLASS__, 'my_save_post', 10, 3) );
        add_action( 'save_post', array( __CLASS__, 'my_save_post', 10, 3) );

        
        add_filter( 'redirect_post_location', array( __CLASS__, 'my_redirect_post_location'), 10, 1);        
    }
        
    // Редирект с сообщением об обнаружении правил в тексте
    public static function my_redirect_post_location( $location ) 
    {        
        remove_filter( 'redirect_post_location', array( __CLASS__, 'my_redirect_post_location'), 10, 1 );                
        return add_query_arg( 'msg', urlencode(self::$msg), $location );
    }

    // Выброс сообщения в админке
    public static function my_admin_notices() 
    {        
        if ( ! isset($_GET['msg']) ) return;

        $stopwords_img_url = STOPWORDS_IMG_URL;
        $stopwords_name = STOPWORDS_NAME;
        
        $msg = mb_substr(strip_tags(@$_GET['msg'], "<b></b><br><p></p>" ), 0, 1000) ;        
        echo <<<DD
        <div class='notice notice-error is-dismissible'><p>
            <img src={$stopwords_img_url} class=stopwords-img>
            <div class=stopwords-info>
                <h2 class=stopwords-h2>{$stopwords_name}</h2>
                <p>{$msg}</p>
            </div>    
            <div class=stopwords-clear></div>            
        </p></div>
DD;
    }    
    
    // Проверка при вставке комментария к посту
    public static function my_wp_insert_comment( $id ) 
    {
        $comment = get_comment( $id );

	if( $comment->comment_approved != 1 || $comment->comment_type != '' /* это именно комментарий а не пинг */ )
		return;
        
        $status = empty(self::checkWords($comment->comment_content)) ? 0 : 'spam';                    
	wp_set_comment_status( $id, $status );  
    }    
}
