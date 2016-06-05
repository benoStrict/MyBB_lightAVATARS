<?php

/* 
 * The MIT License
 *
 * Copyright 2016 MiArz.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

if(!defined("IN_MYBB")) {
    exit;
}

$plugins->hooks['pre_output_page'][5]['LightAvatars->getAvatars']=[
    'class_method' => ['LightAvatars', 'getAvatars']
];

function lightavatars_info() {
    global $lang;
    $lang->load('config_lightavatars');
    
    return [
        "name" => "LightAVATARS",
        "description" => $lang->lightavatars_desc,
        "website" => "",
        "author" => "MiArz",
        "authorsite" => "",
        "version" => "0.9",
        "codename" => "LightAvatars",
        "compatibility" => "18*"
    ];
}

function lightavatars_is_installed() {
    global $db;
    return $db->num_rows(
            $db->simple_select(
                    'settinggroups', 
                    '*', 
                    'name="lightavatars"'
                    )
            );
}

function lightavatars_install() {
    global $db, $cache, $lang;
    $lang->load('config_lightavatars');

    $sgid=$db->insert_query(
            "settinggroups", 
            [
                "name"=>"lightavatars", 
                "title"=>"LightAVATARS", 
                "description"=>$db->escape_string($lang->setting_group_lightavatars_desc)
            ]);

    $sg=[
        [
            'name'=>'lightavatars_custom', 
            'title'=>$lang->lightavatars_custom, 
            'description'=>$lang->lightavatars_custom_desc, 
            'optionscode'=>'yesno', 
            'value'=>'0'
            ],
        [
            'name'=>'lightavatars_link', 
            'title'=>$lang->lightavatars_link, 
            'description'=>$lang->lightavatars_link_desc, 
            'optionscode'=>'text', 
            'value'=>'inherit'
            ],
        [
            'name'=>'lightavatars_img', 
            'title'=>$lang->lightavatars_img, 
            'description'=>$lang->lightavatars_img_desc, 
            'optionscode'=>'text', 
            'value'=>'inherit'
            ],
        [
            'name'=>'lightavatars_forumbit_depth2_forum_lastpost', 
            'title'=>$lang->lightavatars_forumbit_depth2_forum_lastpost, 
            'description'=>$lang->lightavatars_forumbit_depth2_forum_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_forumbit_depth1_forum_lastpost', 
            'title'=>$lang->lightavatars_forumbit_depth1_forum_lastpost, 
            'description'=>$lang->lightavatars_forumbit_depth1_forum_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_forumdisplay_thread_firstpost', 
            'title'=>$lang->lightavatars_forumdisplay_thread_firstpost, 
            'description'=>$lang->lightavatars_forumdisplay_thread_firstpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_forumdisplay_thread_lastpost', 
            'title'=>$lang->lightavatars_forumdisplay_thread_lastpost, 
            'description'=>$lang->lightavatars_forumdisplay_thread_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_search_results_posts_post', 
            'title'=>$lang->lightavatars_search_results_posts_post, 
            'description'=>$lang->lightavatars_search_results_posts_post_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_search_results_threads_thread_firstpost', 
            'title'=>$lang->lightavatars_search_results_threads_thread_firstpost, 
            'description'=>$lang->lightavatars_search_results_threads_thread_firstpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_search_results_threads_thread_lastpost', 
            'title'=>$lang->lightavatars_search_results_threads_thread_lastpost, 
            'description'=>$lang->lightavatars_search_results_threads_thread_lastpost_desc, 
            'optionscode'=>'text', 
            'value'=>'small'
            ],
        [
            'name'=>'lightavatars_google_seo', 
            'title'=>$lang->lightavatars_google_seo, 
            'description'=>$lang->lightavatars_google_seo_desc, 
            'optionscode'=>'text', 
            'value'=>'uid=%userid%'
            ],
        ];

    $i=1;
    foreach ($sg as &$row) {
        $row['gid']=$sgid;
        $row['title']=$db->escape_string($row['title']);
        $row['description']=$db->escape_string($row['description']);
        $row['disporder']=$i++;

    }
    
    $db->insert_query_multiple('settings', $sg);
    rebuild_settings();
    
    $db->delete_query(
            'themestylesheets', 
            'name="lightavatars.css" AND tid=1'
            );
    $styles=require_once __DIR__.'/../../resources/lightavatars.css';
    
    $db->insert_query(
            "themestylesheets", 
            [
                "name"=>"lightavatars.css",
                "cachefile"=>"lightavatars.css",
                "tid"=>"1",
                "attachedto"=>"forumdisplay.php|index.php|search.php",
                "stylesheet"=>$db->escape_string($styles),
                "lastmodified"=>TIME_NOW
            ]
            );
    
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    cache_stylesheet(1,"lightavatars.css",$styles);
    update_theme_stylesheet_list(1);
    
    require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
    find_replace_templatesets(
            'forumdisplay_thread', 
            '#'.preg_quote('{$attachment_count}').'#', 
            '{+LIGHTAVATARS+}forumdisplay_thread_firstpost|{$thread[\'profilelink\']}{+ENDofBLOCK+}{$attachment_count}'
            );
    
    find_replace_templatesets(
            'forumdisplay_thread', 
            '#'.preg_quote('<span class="lastpost smalltext">').'(\s*?)'.preg_quote('{$lastpostdate}').'#', 
            '{+LIGHTAVATARS+}forumdisplay_thread_lastpost|{$lastposterlink}{+ENDofBLOCK+}<span class="lastpost smalltext">\\1{$lastpostdate}'
            );
    
    find_replace_templatesets(
            'forumbit_depth1_forum_lastpost', 
            '#'.preg_quote('<span class="smalltext">').'(\s*?)'.preg_quote('<a href="{$lastpost_link}"').'#', 
            '{+LIGHTAVATARS+}forumbit_depth1_forum_lastpost|{$lastpost_profilelink}{+ENDofBLOCK+}<span class="smalltext">\\1<a href="{$lastpost_link}"'
            );
    
    find_replace_templatesets(
            'forumbit_depth2_forum_lastpost', 
            '#'.preg_quote('<span class="smalltext">').'(\s*?)'.preg_quote('<a href="{$lastpost_link}"').'#', 
            '{+LIGHTAVATARS+}forumbit_depth2_forum_lastpost|{$lastpost_profilelink}{+ENDofBLOCK+}<span class="smalltext">\\1<a href="{$lastpost_link}"'
            );
    
    find_replace_templatesets(
            'search_results_posts_post', 
            '#'.preg_quote('{$post[\'profilelink\']}').'#', 
            '{+LIGHTAVATARS+}search_results_posts_post|{$post[\'profilelink\']}{+ENDofBLOCK+}{$post[\'profilelink\']}'
            );
    
    find_replace_templatesets(
            'search_results_threads_thread', 
            '#'.preg_quote('{$attachment_count}').'#', 
            '{+LIGHTAVATARS+}search_results_threads_thread_firstpost|{$thread[\'profilelink\']}{+ENDofBLOCK+}{$attachment_count}'
            );
    
    find_replace_templatesets(
            'search_results_threads_thread', 
            '#'.preg_quote('<span class="smalltext">').'(\s*?)'.preg_quote('{$lastpostdate}').'#', 
            '{+LIGHTAVATARS+}search_results_threads_thread_lastpost|{$lastposterlink}{+ENDofBLOCK+}<span class="smalltext">\\1{$lastpostdate}'
            );
}

function lightavatars_uninstall() {
    global $db, $cache;
    $db->delete_query(
            'themestylesheets', 
            'name="lightavatars.css" AND tid=1'
            );
    
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    @unlink(MYBB_ROOT."cache/themes/theme1/lightavatars.css");
    @unlink(MYBB_ROOT."cache/themes/theme1/lightavatars.min.css");
    update_theme_stylesheet_list(1);
    
    $db->delete_query("settinggroups", "name=\"lightavatars\"");
    $db->delete_query("settings", "name LIKE \"lightavatars%\"");
    rebuild_settings();
    
    require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
    find_replace_templatesets(
            'forumdisplay_thread', 
            '#\{\+LIGHTAVATARS\+\}(.*?)\{\+ENDofBLOCK\+\}#', 
            ''
            );
    find_replace_templatesets(
            'forumdisplay_thread', 
            '#\{\+LIGHTAVATARS\+\}(.*?)\{\+ENDofBLOCK\+\}#', 
            '' 
            );
    find_replace_templatesets(
            'forumbit_depth1_forum_lastpost', 
            '#\{\+LIGHTAVATARS\+\}(.*?)\{\+ENDofBLOCK\+\}#', 
            ''
            );
    find_replace_templatesets(
            'forumbit_depth2_forum_lastpost', 
            '#\{\+LIGHTAVATARS\+\}(.*?)\{\+ENDofBLOCK\+\}#', 
            ''
            );
    find_replace_templatesets(
            'search_results_posts_post', 
            '#\{\+LIGHTAVATARS\+\}(.*?)\{\+ENDofBLOCK\+\}#', 
            ''
            );
    find_replace_templatesets(
            'search_results_threads_thread', 
            '#\{\+LIGHTAVATARS\+\}(.*?)\{\+ENDofBLOCK\+\}#', 
            ''
            );
    find_replace_templatesets(
            'search_results_threads_thread', 
            '#\{\+LIGHTAVATARS\+\}(.*?)\{\+ENDofBLOCK\+\}#', 
            ''
            );
}

final class LightAvatars
{ 
    
    public function getAvatars(&$content)
    {
        global $db, $mybb;
        
        if(!preg_match_all('#\{\+LIGHTAVATARS\+\}#', $content)) {
            return $content;
        }
        
        preg_match_all('#\{\+LIGHTAVATARS\+\}(.*?)\|<a.*?\"(.*?)\".*?\>(.*?)\</a\>\{\+ENDofBLOCK\+\}#', $content, $params);
        
        $content=preg_replace('/\{\+LIGHTAVATARS\+\}(.*?)\|<a href=\".*?\">(.*?)<\/a>\{\+ENDofBLOCK\+\}/', '{+LIGHTAVATARS+}\\1-\\2{+ENDofBLOCK+}', $content);
        $content=preg_replace('/\{\+LIGHTAVATARS\+\}(.*?)\|.*?\{\+ENDofBLOCK\+\}/', '{+LIGHTAVATARS+}\\1-^{+ENDofBLOCK+}', $content);
           
        /*
         * I NEED
         * @param $param
         * $params[0]
         * $params[1]-guards
         * $params[2]-links
         * $params[3]-names
         * 
         * $loop-create loops => $l i$
         * $iloop-create loop => j$
         * 
         * tester code
         * echo'<pre style="text-align:left">';var_dump($params);echo'</pre>';
         */
        
        $loop=count($params[0]);
        $l=$loop;
        
        
        /*
         * Error swich :D
         */
        
        switch(false) {
            case isset($mybb->settings['lightavatars_google_seo']):
            case strpos($mybb->settings['lightavatars_google_seo'],'%'):
                return 'LA_1 PATTERN ERROR'.$content;
        }
        
        if(strpos($mybb->settings['lightavatars_google_seo'], '%userid%')) {
            $getid=explode('%userid%',$mybb->settings['lightavatars_google_seo']);
            if(!empty($getid[1])) {
                while($l--) {
                    $la[$l]['id']=explode($getid[1], $params[2][$l])[0];
                }
            }
            if(!isset($la)) {
                while($l--) {
                    $la[$l]['id']=$params[2][$l];
                }
            }
            $i=$loop;
            while($i--) {
                    $la[$i]['id']=explode($getid[0], $la[$i]['id'])[1];
                    $info[$params[1][$i]][$la[$i]['id']]=$params[2][$i];
                    
                    $select[$la[$i]['id']]=$la[$i]['id'];
            }
            $selected='uid='.implode(' OR uid=',$select);
        } else {
            $getid=explode('%username%',$mybb->settings['lightavatars_google_seo']);
            if(!empty($getid[1])) {
                while($l--) {
                    $la[$l]['name']=explode($getid[1], $params[2][$l])[0];
                }
            }
            if(!isset($la)) {
                while($l--) {
                    $la[$l]['name']=$params[2][$l];
                }
            }
            $i=$loop;
            while($i--) {
                    $la[$i]['name']=explode($getid[0], $la[$i]['id'])[1];
                    $info[$params[1][$i]][$la[$i]['name']]=$params[2][$i];
                    $select[$la[$i]['name']]=$la[$i]['name'];  
            }
            $selected='username='.implode(' OR username=',$select);
            echo $selected;
        } 
        $avatardata=$db->simple_select(
                "users", 
                "uid,username,avatar", 
                $selected, 
                NULL
                );
        
        $iloop=count($info);
        
        echo'<pre style="text-align:left">';var_dump($info);echo'</pre>';
        
        if(strpos($mybb->settings['lightavatars_google_seo'], '%userid%')) {
            while($base=$db->fetch_array($avatardata)) {
                $j=$iloop;
                while($j--) {
                    
                }
                /*$masterstyle['avatar']=explode(' ',$mybb->settings['lightavatars_'.$info[$base['uid']][1]]);
                echo 'lightavatars_'.$info[$base['uid']][1]."<br>";
                echo'<pre style="text-align:left">';var_dump($masterstyle['avatar']);echo'</pre>';
                /*$masterstyle['avatar']="avatar--".implode(" avatar--",$masterstyle['avatar']);
                $avatartocontent='<div class=""'*/
                $content=str_replace('{+LIGHTAVATARS+}'.$base['username'].'{+ENDofBLOCK+}','cis',$content);
            }
        }
        /*
        while($base=$db->fetch_array($avatardata)) {
            
            $content=str_replace('{+LIGHTAVATARS+}'.$base['username'].'{+ENDofBLOCK+}','cis'.$base['username'],$content);
            echo'<pre style="text-align:left">';
            var_dump($base);
            echo'</pre>';
        }
        
        /*
        
        $urlpattern= explode('%', $mybb->settings['lightavatars_google_seo']);
        if(isset($urlpattern[4])) {
            if($urlpattern[3]==='userid') {
                while($l--) {
                    $la_user['id'][explode($urlpattern[2],$params[2][$l])[1]]=$params[2][$l];
                }
            } else {
                while($l--) {
                    $la_temp=explode($urlpattern[0],$params[2][$l])[1];
                    $la_user['id'][explode($urlpattern[2],$la_temp)[0]]=$params[2][$l];
                }
            }
        } else {
            if($urlpattern[1]==='userid') {
                while($l--) {
                    $la_user['id'][explode($urlpattern[0], $params[2][$l])[1]]=$params[2][$l];
                }
            } else {
                while($l--) {
                    $la_user['name'][explode($urlpattern[0],$params[2][$l])[1]]=$params[2][$l];
                }
            }
        }
        
        echo'<pre style="text-align:left">';
        var_dump($la_user);
        echo'</pre>';
        
        $i=$loop;
        $new_loop=0;
        if(isset($la_user['id'])) {
            while($i--) {
                $toreplace[$i]=explode('{%ENDofGUARD%}',$params[0][$i]);
                $content=str_replace($toreplace[$i][1], '{-%AVATAR%}'.$la_user['id'][$i].'{%ENDofAVATAR%}', $content);
                if(preg_match('#{-%AVATAR%}'.$la_user['id'][$i].'{%ENDofAVATAR%}#', $content)){
                    $new_loop++;
                    
                }
            }
            while($new_loop--) {
                $la_final[$i]=[
                    $la_user['id'][$i] => 1,
                    $params[2][$i] => 1,
                    $params[3][$i] => 1
                    
                ];
            }
            
            if(preg_match('#{%AVATAR%}.*?{%ENDofAVATAR%}#',$content)) {
                $content=preg_replace('#{%AVATAR%}.*?{%ENDofAVATAR%}#', '{-%AVATAR%}0{%ENDofAVATAR%}', $content);
            }
            foreach($la_user['id'] as $la_id) {
                if(!empty($la_id)) {
                    $select[]=$la_id;
                }
            }
            $selected='uid='.implode(' OR uid=',$select);
            
        } else {
            while($i--) {
                $toreplace[$i]=explode('{%ENDofGUARD%}',$params[0][$i]);
                $content=str_replace($toreplace[$i][1], '{-%AVATAR%}'.$la_user['name'][$i].'{%ENDofAVATAR%}', $content);
                $la_final[$la_user['name'][$i]]=[$params[2][$i]];
            }
            if(preg_match('#{%AVATAR%}.*?{%ENDofAVATAR%}#',$content)) {
                $content=preg_replace('#{%AVATAR%}.*?{%ENDofAVATAR%}#', '{-%AVATAR%}0{%ENDofAVATAR%}', $content);
            }
            
            foreach($la_user['name'] as $la_name) {
                if(!empty($la_name)) {
                    $select[]=$la_name;
                }
            }
            $selected='username='.implode(' OR username=',$select);
        }
        
        $avatardata=$db->simple_select("users", 
                "uid,username,avatar", 
                $selected, 
                NULL
                );
        
        echo '<pre style="text-align:left">';
        var_dump($la_final);
        echo'</pre>';
        while($base=$db->fetch_array($avatardata)){
           
            if($mybb->settings['lightavatars_custom']) {
                $style['avatar']=' avatar';
                $style['block']=' avatar__block avatar_block--'.$base['username'];
                $style['link']=' avatar__link avatar__link--'.$base['username'];
                $style['img']=' avatar__img avatar__img--'.$base['username'];
            }
            
            if(!empty($mybb->settings['lightavatars_block'])){ 
                $masterstyle['block']=explode(' ',$mybb->settings['lightavatars_block']);
                $masterstyle['block']="avatar__block--".implode(" avatar__block--",$masterstyle['block']);
            }
            
            if(!empty($mybb->settings['lightavatars_link'])){ 
                $masterstyle['link']=explode(' ',$mybb->settings['lightavatars_link']);
                $masterstyle['link']="avatar__link--".implode(" avatar--__link",$masterstyle['link']);
            }
            
            if(!empty($mybb->settings['lightavatars_img'])){ 
                $masterstyle['img']=explode(' ',$mybb->settings['lightavatars_img']);
                $masterstyle['img']="avatar__img--".implode(" avatar--__img",$masterstyle['img']);
            }
            
            
            $avatars[$base['uid']]='<div class="'.$masterstyle['block'].$style['block'].'"><a href="'.$array[$base['uid']][0].'" class="'.$masterstyle['link'].$style['link'].'" title="'.$array[$base['uid']][1].'" rel="nofollow"><img src="';
            if($base['avatar']) {$avatars[$base['uid']].=$base['avatar'];} else {$avatars[$base['uid']].=$mybb->settings['useravatar'];}
            $avatars[$base['uid']].='" alt="'.$array[$base['uid']][1].' avatar image" class="'.$masterstyle['img'].$style['img'].'" onerror="this.src=\''.$mybb->settings['useravatar'].'\'"/></a></div></div>';
            
        }
        $avatars[0]='<div class="'.$masterstyle['block'].' avatar__block"><img src="'.$mybb->settings['useravatar'].'" alt="default avatar image" class="'.$masterstyle['img'].' avatar__img" onerror="this.src=\''.$mybb->settings['useravatar'].'\'"/></div></div>';
        
        while($loop--) {
            if(!isset($avatars[$match[$loop]])){
                $avatars[$match[$loop]]=$avatars[0];
            }

            $masterstyle['avatar']=explode(' ',$mybb->settings['lightavatars_'.$params[1][$loop]]);
            $masterstyle['avatar']="avatar--".implode(" avatar--",$masterstyle['avatar']);
            $content=str_replace(
                    '{%GUARD%}'.$params[1][$loop].'{%ENDofGUARD%}{%AVATAR%}'.$match[$loop].'{%ENDofAVATAR%}', 
                    '<div class='.$masterstyle['avatar'].$style['avatar'].'>'.$avatars[$match[$loop]], 
                    $content);
        }
        */
    }
}
