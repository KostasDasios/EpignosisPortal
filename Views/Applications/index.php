<h1>Applications</h1>
<div class="row col-md-12 centered">
    <table class="table table-striped custab">
        <thead>
        <a href="/EpignosisPortal/applications/create/" class="btn btn-primary btn-xs pull-right"><b>+</b> Add new application</a>
        <tr>
            <th>Date Submitted</th>
            <th>Dates Requested</th>
            <th>Status</th>
        </tr>
        </thead>
        <?php
        foreach ($applications as $application)
        {
            echo '<tr>';
            echo "<td>" . $application['created_at'] . "</td>";
            echo "<td>";

                $date_from = date_create($application['date_from']);
                $date_to = date_create($application['date_to']);
                $diff = date_diff($date_from,$date_to);
                echo str_replace("+","",$diff->format("%R%a days"));

            echo "</td>";
            echo "<td>" . ucfirst($application['status']) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>