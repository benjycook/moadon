<form method="POST"  data-parsley-validate>
  <?php

    if($error)
    {
      ?>
        <p class="error_msg"><?= $error?></p>
      <?php
    }
  ?>
      <div class="row">
        <div class="form-field half" style="width:100%;">
            <div class="form-group">
              <label for="email">שם משתמש(דוא"ל):</label>
              <input type="text" id="email" name="email" value='<?= $email ?>' required="required" >
            </div>
        </div>
      </div>
      
      <div class="row">
          <div class="form-field half" style="width:100%;">
            <div class="form-group">
              <label for="password">סיסמא:</label>
              <input type="password" name="password" value='<?= $password ?>' required="required" >
                </div>
          </div>
      </div>

      
      <input type="submit"  value="כניסה" name="login" class="btn btn-defualt">
</form>