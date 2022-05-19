<?php

$host = '';
$db = '';

$user = '';
$pass = '';


if (isset($_REQUEST['search']) && $_REQUEST['search']):
	$hits = [];
	$search = $_REQUEST['search'];

	$mysqli = new mysqli($host, $user, $pass, $db);
	$result = $mysqli->query('SELECT `table_name` FROM information_schema.tables WHERE `table_schema` = \'' . $db . '\'');

	while ($row = $result->fetch_assoc()) {
		$name = $row['table_name'];

		$columns = ($mysqli->query('SHOW COLUMNS FROM ' . $name . ';'))->fetch_all();

		$where = implode(
			' OR ',
			array_map(
				static function($item) use ($search) {
					return '`' . $item[0] . '` LIKE \'%' . $search . '%\'';
				},
				$columns
			)
		);

		$hit = $mysqli->query('SELECT id FROM `' . $name . '` WHERE ' . $where);

		if ($hit) {
			$hits[$name] = array_values(array_column($hit->fetch_all(), 0));
		}
	}
	?>
<table>
	<thead>
	<th>Table</th>
	<th>ID</th>
	</thead>
	<tbody>
	<?php foreach ($hits as $table => $ids): ?>
	<tr>
		<td><?php echo $table ?></td>
		<td>
		<ul>
			<?php foreach ($ids as $id): ?>
				<li><?php echo $id ?></li>
			<?php endforeach; ?>
		</ul>
		</td>
	</tr>
	<?php endforeach ?>
	</tbody>
</table>
	<form>
		<input name="search" type="text" />
	</form>
<?php
else:
?>
<form>
	<input name="search" type="text" />
</form>

<?php endif; ?>
