<?php

require_once 'convert.php';

$text = $_POST['source'];

if (empty($text)) :
?>
<center>
<h1>Test your page in MarkDown format</h1>

<form action="./test.php" method="post" target="_blank">
<textarea style="width:90%;height:400px;" name="source" placeholder="paste your text here..."></textarea>
<br/><br/>
<input type="submit" value="Submit"/>
</form>
</center>
<?php
else:

echo convertText($text, '#');

endif;


