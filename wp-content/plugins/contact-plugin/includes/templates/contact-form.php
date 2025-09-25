<form id="enquiry_form">
    <label>Name</label>
    <input type="text" name="name">
    <label>Email</label>
    <input type="text" name="email">
    <label>Phone</label>
    <input type="text" name="phone">

    <textarea name="message"></textarea>
    <button type="submit">Submit form</button>
</form>


<script>
    jQuery(document).ready(function ($) {

        $("#enquiry_form").submit(function (event) {

            event.preventDefault();

            var form = $(this);

            $.ajax({
                type: "POST",
                url: "<?php echo get_rest_url(null, 'v1/contact-form/submit'); ?>",
                data: form.serialize()
            })
        })
    })
</script>