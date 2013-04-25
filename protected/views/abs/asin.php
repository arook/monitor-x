<table class="table table-striped">
	<caption><?php echo $asin ?></caption>

    <thead>
        <tr>
            <th>DT</th>
						<?php foreach ($history as $item): ?>
							<th><?php echo date("Y-m-d", $item->dt->sec); ?></th>
						<?php endforeach ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Rank</th>
						<?php foreach ($history as $item): ?>
							<td><?php echo $item->rank ?></td>
						<?php endforeach ?>
        </tr>
    </tbody>
</table>