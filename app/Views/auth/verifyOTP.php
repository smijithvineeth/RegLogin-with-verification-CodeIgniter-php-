<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <h4>Email Verification</h4>
                <?php if (!empty(session()->getFlashdata('fail'))) : ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('fail'); ?></div>
                <?php endif; ?>
                <p>Enter the OTP sent to your email: <?= session()->getFlashdata('email'); ?></p>
                <form action="<?= base_url('auth/verifyOTP'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="form-group">
                        <label for="otp">OTP</label>
                        <input type="text" id="otp" name="otp" class="form-control">
                        <span class="text-danger"><?= isset($validation) ? display_error($validation, 'otp') : ''; ?></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Verify OTP</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
