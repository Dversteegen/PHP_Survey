<?php

// By making this a function, you just have to call this function to get the navigation bar on the correct page.
// There's two different classes for the logo and actual navigation bar, which makes it possible to properly use display flex.
// The navigation bar is basically just an unordered list with hyperlinks as items.
function GetNavigationBar()
{
    
?>
<header id="header_navigation_bar">

    <div id="logo_navigation_bar">
        <a href="index.php">
        <h3>PotatoWeb</h3>
        </a>
    </div>

    <nav id="navigation_bar">
        <ul>
            <li><a href="index.php">Home</a></li>            
        </ul>
    </nav>
</header>
<?php    
}