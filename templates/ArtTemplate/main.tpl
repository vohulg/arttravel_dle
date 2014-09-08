<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
{headers}
<link rel="shortcut icon" href="{THEME}/images/favicon.ico" />
<link media="screen" href="{THEME}/style/styles.css" type="text/css" rel="stylesheet" />
<link media="screen" href="{THEME}/style/stylesCms.css" type="text/css" rel="stylesheet" />
<link media="screen" href="{THEME}/style/cssSidebar.css" type="text/css" rel="stylesheet" />
<link media="screen" href="{THEME}/style/engine.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="{THEME}/js/libs.js"></script>
</head>
<body>
{AJAX}
<div class="wrapper">

	<div class="header" >            
                <h1><a href="/index.php" title="Arttravel"> </a></h1>
                <div class="phone"> 
                    <div class="insidephone">
                   <span>+7 727 317 35 09, 328 00 49, 232 45 55 </span>
                   <span>art_travel@mail.ru </span>
                   </div>
                </div>
        </div><!-- .header-->
        
        <div id="beformenubar">
        <div  id="menubar">
            <div id="insidemenubar">
             <div class="menubar" >
			{include file="topmenu.tpl"}
		</div>
                </div>
		    
	       
            
        </div>
                </div>
        
        
        

	<div class="middle">

		<div class="container">
                    <div class="navigation">{speedbar} </div>
			<main class="content">
                             {info}
                                {content}
			</main><!-- .content -->
		</div><!-- .container-->

		<aside class="right-sidebar">
                    גאנגאאננאננאנ
		</aside><!-- .right-sidebar -->

	</div><!-- .middle-->

</div><!-- .wrapper -->
<prefooter class="prefooter">
</prefooter><!-- .footer -->
<footer class="footer">
</footer><!-- .footer -->
</body>
</html>