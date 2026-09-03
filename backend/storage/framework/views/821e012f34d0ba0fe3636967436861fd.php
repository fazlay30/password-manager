<h4>Hello, <?php echo e($senderName); ?> has send you a group invitation.</h4>

<button class="btn btn-success"><a href="<?php echo e(config('app.frontend_url') . '/accept-invitation/' . $token); ?>">Join @ <?php echo e($groupProject->name); ?></a></button>
<?php /**PATH C:\laragon\www\hashlock-backend\resources\views/emails/groupInvitationEmail.blade.php ENDPATH**/ ?>