<?php
if (!defined('PASDNKFWIE')) {
   die('Illegal Access.');
   exit;
} 

if (isset($_REQUEST['email']))
//if "email" is filled out, send email
  {
  //send email
  $email = $_REQUEST['email'] ; 
  $subject = "Enquiry from Title";
  $message = "\nEnquiry from Title\n\nName\t: " . $_REQUEST['name'] . "\nTelephone number\t: " . $_REQUEST['number'] . "\n\nMessage:\n" . $_REQUEST['message'] ;
  mail( ENQUIRY_EMAIL, $subject,
  $message, "From: $email" );
  echo "<b>Thank you for using our mail form. We will get back to you shortly.</b>";
  }

?>
<script language="JavaScript" type="text/javascript">
function isEmail(str) {
  // are regular expressions supported?
  var supported = 0;
  if (window.RegExp) {
    var tempStr = "a";
    var tempReg = new RegExp(tempStr);
    if (tempReg.test(tempStr)) supported = 1;
  }
  if (!supported) 
    return (str.indexOf(".") > 2) && (str.indexOf("@") > 0);
  var r1 = new RegExp("(@.*@)|(\\.\\.)|(@\\.)|(^\\.)");
  var r2 = new RegExp("^.+\\@(\\[?)[a-zA-Z0-9\\-\\.]+\\.([a-zA-Z]{2,3}|[0-9]{1,3})(\\]?)$");
  return (!r1.test(str) && r2.test(str));
}
function checkform ( form )
{
  // see http://www.thesitewizard.com/archive/validation.shtml
  // for an explanation of this script and how to use it on your
  // own website

  // ** START **
  if (form.email.value == "" && form.number.value == "") {
    alert( "Please enter either your email address or your phone number." );
    form.email.focus();
    return false ;
  }
  if (form.email.value != "") {
    if (isEmail(form.email.value) == false) {
      alert( "Please enter a valid email address." );
      form.email.focus();
      return false ;
    }
  }
  // ** END **
  return true;
}
</script>
<div class="box_big" style="position:relative;text-align:left;">
<div style="text-align:left;padding:0px 95px 0px 10px;background-color:#DBE8F0;border:2px solid #AAAAAA;font-family:Verdana, Arial, sans-serif;font-size:12px;line-height:1.5;">
&nbsp;<p>
<h1>Contact Us</h1>
<p>
  <form method='post' action='' onsubmit='return checkform(this);'>
    <table border="0" cellpadding="0" cellspacing="0" width="700" align="center" >
      <tr>
	<td width="150" align="left" valign="center">
	  &nbsp;&nbsp;&nbsp;Name:
	</td>
	<td width="450" align="left" valign="center">
	  <input name='name' size='50' type='text' />
	</td>
      </tr>

      <tr>
	<td width="150" align="left" valign="center" height="10"></td>
      </tr>
      <tr>
	<td width="150" align="left" valign="center">
	  &nbsp;&nbsp;&nbsp;Email:
	</td>
	<td width="450" align="left" valign="center">
	  <input name='email' size='50' type='text' />
	</td>
      </tr>
      <tr>
	<td width="150" align="left" valign="center" height="10"></td>
      </tr>
      <tr>
	<td width="150" align="left" valign="center">
	  &nbsp;&nbsp;&nbsp;Contact Number:  
	</td>
	<td width="450" align="left" valign="center">
	  <input name='number' size='50' type='text' />
	</td>
      </tr>
      <tr>
	<td width="150" align="left" valign="center" height="10"></td>
      </tr>  
      <tr>
	<td width="150" align="left" valign="top">
	  &nbsp;&nbsp;&nbsp;Enquiry: 
	</td>
	<td width="450" align="left" valign="top">
	  <textarea name='message' rows='8' cols='50'></textarea>
	</td>
      </tr>
      <tr>
	<td width="150" align="left" valign="center" height="10"></td>
      </tr>
      <tr>
	<td width="150" align="left" valign="top">
	</td>
	<td width="450" align="left" valign="top">
	  <input type='submit' value='Submit Enquiry' />
	</td>
      </tr> 
      <tr>
	<td width="150" align="left" valign="center" height="10"></td>
      </tr>
    </table>
  </form>
<p>&nbsp;<p></div>
</div>