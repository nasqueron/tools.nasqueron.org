<?php
        $result = '';
	if (array_key_exists('operation', $_REQUEST)) {
		require('ListOperation.php');
		$operation = ucfirst($_REQUEST["operation"]);
		if (method_exists('ListOperation', $operation)) {
			$left = explode("\n", $_REQUEST['lists'][0]);
			$right = explode("\n", $_REQUEST['lists'][1]);
			$result = implode("\n", ListOperation::$operation($left, $right));
		} else {
			$result = "// Unknown list operation.";
		}
	}
?>
    <form name="lists" method="post" action="/lists/operations">
    <div class="large-12 columns">
      <div class="row">
        <div class="large-3 columns">
          <h6 class="panel" style="text-align: center;">List A</h6>
          <textarea name="lists[0]" rows=20><?= $_REQUEST['lists'][0] ?></textarea>
        </div>

        <div class="large-1 columns">
          <h6 class="panel" style="text-align: center;">Operation</h6>
          <label for="operation_add"><input name="operation" value="add" type="radio" id="operation_add"> <i class="gen-enclosed foundicon-plus"> add</i></label>
          <label for="operation_intersect"><input name="operation" value="intersect" CHECKED type="radio" id="operation_intersect"> <i class="gen-enclosed foundicon-remove"> intersect</i></label>
          <label for="operation_substract"><input name="operation" value="substract" type="radio" id="operation_substract"> <i class="gen-enclosed foundicon-minus"> substract</i></label>
          <p>&nbsp;</p>
          <p><input type="submit" value="Compute" class="button" /></p>
        </div>

        <div class="large-3 columns">
          <h6 class="panel" style="text-align: center;">List B</h6>
          <textarea name="lists[1]" rows=20><?= $_REQUEST['lists'][1] ?></textarea>
        </div>

        <div class="large-3 columns">
          <h6 class="panel" style="text-align: center;">Result</h6>
          <textarea name="lists[2]" rows=20><?= $result ?></textarea>
        </div>
      </div>
    </div>
    <div style="clear: both;" />
    </form>
