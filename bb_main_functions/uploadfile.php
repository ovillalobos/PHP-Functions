<?php 
// In PHP 4.1.0 or later, $_FILES should be used instead of $HTTP_POST_FILES.
if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
    copy($HTTP_POST_FILES['userfile']['tmp_name'], "/files");
} else {
    echo "Possible file upload attack. Filename: " . $_FILES['userfile']['name'];
}
/* ...or... */
move_uploaded_file($_FILES['userfile']['tmp_name'], "/files");
?> 