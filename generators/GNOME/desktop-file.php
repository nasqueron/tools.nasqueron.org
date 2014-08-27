<style>
#exec-variables, #icon-factory {
    display: none;
}
</style>
<?php
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'Application';
	$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
	$exec = isset($_REQUEST['exec']) ? $_REQUEST['exec'] : '';
	$icon = isset($_REQUEST['icon']) ? $_REQUEST['icon'] : '';
	$comment = isset($_REQUEST['comment']) ? $_REQUEST['comment'] : '';
	$encoding = isset($_REQUEST['encoding']) ? $_REQUEST['encoding'] : 'UTF-8';
	$terminal = isset($_REQUEST['terminal']) ? (bool)$_REQUEST['terminal'] : false;
?>
<form class="custom" name="data">
  <div class="row">
  <div class="six columns">
  <h3>Properties</h3>
  <div class="row">
    <div class="two mobile-one columns">
      <label class="right inline" for="type">Type:</label>
    </div>
    <div class="ten mobile-three columns">
      <input type="text" id="type" name="type" value="<?= $type ?>" class="six" onChange="File.Generate();" />
    </div>
  </div>
  <div class="row">
    <div class="two mobile-one columns">
      <label class="right inline" for="encoding">Encoding:</label>
    </div>
    <div class="ten mobile-three columns">
      <input type="text" id="encoding" name="encoding" value="<?= $encoding ?>" class="six" onChange="File.Generate();" />
    </div>
  </div>
  <div class="row">
    <div class="two mobile-one columns">
      <label class="right inline" for="name">Name:</label>
    </div>
    <div class="ten mobile-three columns">
      <input type="text" name="name" id="name" value="<?= $name ?>" placeholder="Application name, usually starting by an uppercase" onChange="File.Generate();" />
    </div>
  </div>
  <div class="row">
    <div class="two mobile-one columns">
      <label class="right inline" for="comment">Comment:</label>
    </div>
    <div class="ten mobile-three columns">
      <input type="text" name="comment" id="comment" value="<?= $comment ?>" placeholder="Comment, formerly used as tooltip, doesn't seem in use currently." onChange="File.Generate();" />
    </div>
  </div>
  <div class="row">
    <div class="two mobile-one columns">
      <label class="right inline" for="exec">Exec:</label>
    </div>
    <div class="ten mobile-three columns">
      <input type="text" name="exec" id="exec" value="<?= $exec ?>" placeholder="Command to execute." onFocus="CheatSheet.Print('exec-variables')" onChange="File.Generate();" />
    </div>
  </div>
  <div class="row">
    <div class="two mobile-one columns">
      <label class="right inline" for="icon">icon:</label>
    </div>
    <div class="ten mobile-three columns">
      <input type="text" name="icon" id="icon" value="<?= $icon ?>" placeholder="Icon filename or path." onFocus="CheatSheet.Print('icon-factory')" onChange="File.Generate();" />
    </div>
  </div>
  <div class="row">
    <div class="two mobile-one columns">
      <label class="right inline">Terminal:</label>
    </div>
    <div class="ten mobile-three columns">
     <label for="terminal-true"><input name="terminal" type="radio"<?= $terminal ? ' CHECKED' : '' ?> id="terminal-true" value="True" onChange="File.Generate();"> True</label>
     <label for="terminal-false"><input name="terminal" type="radio"<?= $terminal ? '' : ' CHECKED' ?> id="terminal-false" value="False" onChange="File.Generate();"> False</label>
    </div>
  </div>
  <div class="row">
    <h3>File</h3>
    <textarea id="file" name="file" rows="8" onChange="File.Generate();"></textarea>
  </div>
  </div>
  <div class="six columns">
   <div id="exec-variables">
    <h3>Exec variables</h3>
    <table summary="Exec variables" style="border: solid 1px;">
<thead><tr>
<th class="td-colsep">Add...</th>
<th>Accepts...</th>
</tr></thead>
<tbody>
<tr>
<td class="td-colsep"><span class="command" dir="ltr">%f</span></td>
<td>a single filename.</td>
</tr>
<tr class="tr-shade">
<td class="td-colsep"><span class="command" dir="ltr">%F</span></td>
<td>multiple filenames.</td>
</tr>
<tr>
<td class="td-colsep"><span class="command" dir="ltr">%u</span></td>
<td>a single URL.</td>
</tr>
<tr class="tr-shade">
<td class="td-colsep"><span class="command" dir="ltr">%U</span></td>
<td>multiple URLs.</td>
</tr>
<tr>
<td class="td-colsep"><span class="command" dir="ltr">%d</span></td>
<td>a single directory.  Used in conjunction with
          <span class="command" dir="ltr">%f</span> to locate a file.</td>
</tr>
<tr class="tr-shade">
<td class="td-colsep"><span class="command" dir="ltr">%D</span></td>
<td>multiple directories.  Used in conjunction with
          <span class="command" dir="ltr">%F</span> to locate files.</td>
</tr>
<tr>
<td class="td-colsep"><span class="command" dir="ltr">%n</span></td>
<td>a single filename without a path.</td>
</tr>
<tr class="tr-shade">
<td class="td-colsep"><span class="command" dir="ltr">%N</span></td>
<td>multiple filenames without paths.</td>
</tr>
<tr>
<td class="td-colsep"><span class="command" dir="ltr">%k</span></td>
<td>a URI or local filename of the location of the
          desktop file.</td>
</tr>
<tr class="tr-shade">
<td class="td-colsep"><span class="command" dir="ltr">%v</span></td>
<td>the name of the Device entry.</td>
</tr>
</tbody>
</table>
 </div>
 <div id="icon-factory">
  <h3>Icon</h3>
  <h5>UNIX path</h5>
  <p>SVG icons are stored in /usr/local/share/icons/hicolor/scalable/apps</p>
  <p>Non scalable icons are sotred in /usr/local/share/icons/hicolor/&lt;size&gt;/apps</p>
  <p>Use 96, 128 or 256 to ensure good result in GNOME3.</p>
  <h5>Linux path</h5>
  <p>Prefix is /usr and not /usr/local.</p>
  <p>SVG icons are stored in /usr/share/icons/hicolor/scalable/apps</p>
  <p>
 </div>
  </div>
  </div>
</form>
