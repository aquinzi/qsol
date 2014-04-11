<!DOCTYPE html>
<html>
<meta charset="utf-8">
<body>

<form method="get" action="search.php">
<input type="search" size="30" name="searchthis">
<input type="submit" value="Search" />
</form>

Advanced search:
<ul>
	<li><code>d</code> cant decks. Can range as 1-4</li>
	<li><code>r</code> level. When using numbers can range as 1-2. (1 very easy, 2 easy, 3 medium, 4 hard, 5 very hard). If you prefer typing the level, remove the spaces. Ex. very easy -&gt; veryeasy. </li>
	<li><code>t</code> type. ex: klondike</li>
</ul>

<p>In <code>d</code> and <code>r</code> when using numbers, can skip the <code>:</code>.</p>

<p>Example of query: <code>d1-2 r:veryeasy t:klondike</code></p>

<p>Or use below:</p>

<form method="get" action="search.php">

<p>Decks:
from  <input type="number" min="1" max="4" value="4" name="dm"> 
to <input type="number" min="1" max="4" value="4" name="dx">

<p> Difficulty: 
from 
<select name="rm"> 
  <option selected value="all" >all</option>
  <option value="1">very easy</option>
  <option value="2">easy</option>
  <option value="3">medium</option>
  <option value="4">hard</option>
  <option value="5">very hard</option>
</select>
to 
<select name="rx"> 
  <option selected value="none" >-</option>
  <option value="1">very easy</option>
  <option value="2">easy</option>
  <option value="3">medium</option>
  <option value="4">hard</option>
  <option value="5">very hard</option>
</select>

<p> Type

<select name="t"> 
  <option selected value="all">all</option>
  <option value="klondike" >klondike</option>
  <option value="canfield" >canfield</option>
  <option value="yukon" >yukon</option>
</select>

<input type="submit" value="Search" />
</form>


</body>
</html>