<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('')?>">
    <title><?= $title;?></title>
</head>
<body>
    <!--copy the code from the dashboard -->
    <h5><?= $title;?></h5>
    <table>
        <thread>
            <tr>
                <th>name</th>
                <th>email</th>
                <th></th>
            </tr>
        </thread>
        <tbody>
            <tr>
                <td><?= ucfirst($userInfo['name']);?></td>
                <td><?= $userInfo['email'];?></td>
                <td><a href="<?= site_url('auth/logout'); ?>">logout</a></td>
            </tr>
        </tbody>
    </table>
</body>
</html>