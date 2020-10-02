<h1>Users</h1>
<div class="row col-md-12 centered">
    <table class="table table-striped custab">
        <thead>
        <a href="/EpignosisPortal/users/create/" class="btn btn-primary btn-xs pull-right"><b>+</b> Add new user</a>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Type</th>
            <th class="text-center">Action</th>
        </tr>
        </thead>
        <?php
        foreach ($users as $user)
        {
            echo '<tr>';
            echo "<td>" . $user['first_name']. "</td>";
            echo "<td>" . $user['last_name'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . ($user['type'] == 1 ? 'Admin' : 'Employee') . "</td>";
            echo "<td class='text-center'><a class='btn btn-info btn-xs' href='/EpignosisPortal/users/edit/" . $user["id"] . "' ><span class='glyphicon glyphicon-edit'></span> Edit</a> <a href='/EpignosisPortal/users/delete/" . $user["id"] . "' class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-remove'></span> Del</a></td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>