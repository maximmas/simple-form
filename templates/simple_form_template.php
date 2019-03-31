<?php
// Шаблон формы

?>

<section class="sf_form">
  <form method="POST">
      <fieldset>
        <legend>Lets get in touch</legend>
        <p>
          <label for="client_name">Name <em>*</em></label>
          <input type="text" class="client_name" name="client_name" required>
        </p>
        <p>
          <label for="client_email">E-mail <em>*</em></label>
          <input type="email" class="client_email" name="client_email" required>
        </p>
        <p>
          <label for="client_message">Message</label>
          <textarea class="client_message" name="client_message"></textarea>
        </p>
      </fieldset>
      <p>
        <input type="submit" value="submit" class="client_submit">
      </p>
    </form>

</section>

<?php 
    require_once ( plugin_dir_path( __FILE__ ) . 'modal_template.php');
?>