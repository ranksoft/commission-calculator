<h2>Commissions</h2>
<table>
    <thead>
    <tr>
        <th>Commission</th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($commissions)): ?>
        <?php foreach ($commissions as $commission): ?>
            <tr>
                <td><?= htmlspecialchars($commission) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td>No commissions available.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
