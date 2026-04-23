<?php $__env->startComponent('mail::message'); ?>

# <?php echo new \Illuminate\Support\EncodedHtmlString($title); ?>


<?php echo new \Illuminate\Support\EncodedHtmlString($message); ?>


<?php if(!empty($data)): ?>
<?php $__env->startComponent('mail::table'); ?>
| Parameter | Nilai |
| :-------- | :---- |
<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
| **<?php echo new \Illuminate\Support\EncodedHtmlString($key); ?>** | <?php echo new \Illuminate\Support\EncodedHtmlString($value); ?> |
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>

<?php if($buttonText && $buttonUrl): ?>
<?php $__env->startComponent('mail::button', ['url' => $buttonUrl, 'color' => $alertType == 'warning' ? 'orange' : ($alertType == 'danger' ? 'red' : 'green')]); ?>
<?php echo new \Illuminate\Support\EncodedHtmlString($buttonText); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>

Hormat kami,<br>
**Berliano Leather**
---
*Ini adalah email otomatis dari sistem produksi. Mohon tidak membalas email ini.*

<?php echo $__env->renderComponent(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/emails/system/alert.blade.php ENDPATH**/ ?>