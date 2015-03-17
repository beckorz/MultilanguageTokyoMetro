Tokyo Metro Multilanguage
==========
このアプリケーションは東京メトロが提供する様々な情報を多言語対応したものです。  
駅、路線、時刻表の情報や運行情報などをMicrosoft Translatorによって機械翻訳を行っています。  

http://needtec.sakura.ne.jp/mtm


インストール方法
-----------------
1.Gitよりコードを取得して、Webサーバーに配置します。  

    git clone git://github.com/mima3/MultilanguageTokyoMetro.git
    cp -rf MultilanguageTokyoMetro /home/xxx/www/

2.必要なディレクトリを作成します。  

    cd /home/xxx/www/MultilanguageTokyoMetro/
    mkdir logs
    mkdir cache
    mkdir compiled

3.composerにより依存ファイルのインストールを行います。  

    cd /home/xxx/www/MultilanguageTokyoMetro/
    php ~/composer.phar self-update 
    php ~/composer.phar install

4.default.htaccessを参考に.htaccessを作成します。  

5.config.php.defaultを参考にconfig.phpを作成します。  
この際、以下のように、非公開の領域のconfig.phpを参照するようにWebサーバー中のconfig.phpを指定するといいでしょう。

    <?php
    require_once '/home/xxx/private/config.php';

キャッシュの更新
-----------------
駅情報、時刻表や言語情報のキャッシュを以下のコマンドで更新します.  

    #キャッシュの更新
    #ダイヤの更新などがあったら行う
    php script/updatecache.php
    
    #言語の更新
    php script/updatelang.php

定期処理の実行
-----------------
列車位置情報、運行情報を以下のコマンドで更新します。 cronで実行するといいでしょう。  

    /usr/local/bin/php /home/xxxxx/www/mtm/script/check_traininfolog.php>/home/needtec/tokyometoro/traininfo.log


ライセンス
-------------
当方が作成したコードに関してはMITとします。  
その他、jqueryなどに関しては、それぞれにライセンスを参照してください。

    The MIT License (MIT)

    Copyright (c) 2015 m.ita

    Permission is hereby granted, free of charge, to any person obtaining a copy of
    this software and associated documentation files (the "Software"), to deal in
    the Software without restriction, including without limitation the rights to
    use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
    the Software, and to permit persons to whom the Software is furnished to do so,
    subject to the following conditions:

    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
    FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
    COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
    IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
    CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

