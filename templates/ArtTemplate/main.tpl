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
                   <span><em>art_travel@mail.ru </em></span>
                   
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
			<div class="content">
                             {info}
                                {content}
			</div><!-- .content -->
                        
                        <div class="right-sidebar">
                    
                        <div class="block">                    
                        
                        <div class="block-image">
                            <img src="{THEME}/images/plane.png" >
                         </div>
                         
                         <div class="block-title">
                                <span> <a href="#" >АВИАБИЛЕТЫ</a> </span>                                
                           </div>              
                         
                         <div class="block-text">
                              <span> <a href="#" >купить авиабилеты</a> </span>
                             </div>                           
                        
                        </div>
                         
                         <div class="block">                    
                        
                        <div class="block-image">
                            <img src="{THEME}/images/plane.png" >
                         </div>
                         
                         <div class="block-title">
                                <span> <a href="#" >АВИАБИЛЕТЫ</a> </span>                                
                           </div>              
                         
                         <div class="block-text">
                              <span> <a href="#" >купить авиабилеты</a> </span>
                             </div>                           
                        
                        </div>
                         
                          <div class="newblock">                    
                        
                               
                         
                         <div class="block-1"> 
                             <div class="block-image">
                            <img src="{THEME}/images/plane.png" >
                         </div>
                         
                         <div class="block-title">
                                <span> <a href="#" >АВИАБИЛЕТЫ</a> </span>                                
                           </div>  
                             
                          </div>
                         
                         <div class="block-2">      
                         
                          <div class="block-text">
                              <span> <a href="#" >купить авиабилеты</a> </span>
                             </div> 
                             
                             </div>
                        
                        </div>
		</div><!-- .right-sidebar -->
                        
                        
		</div><!-- .container-->

		

	</div><!-- .middle-->

</div><!-- .wrapper -->
<prefooter class="prefooter">
</prefooter><!-- .footer -->
<div class="footer">
</div><!-- .footer -->
</body>
</html>