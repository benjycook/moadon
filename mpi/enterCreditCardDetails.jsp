<%@ page language="java" contentType="text/html; charset=utf-8"
	pageEncoding="utf-8" errorPage="/error.jsp"%>
<jsp:useBean id="transactionDetails" scope="request" type="com.creditguard.common.transactions.TransactionDetails" />
<%@ page import="java.text.DecimalFormat" %>
<%@ include file="/merchantPages/ResponsiveWebSources/includes/main.jsp" %>

<%! DecimalFormat formatter = new DecimalFormat("0.00"); %>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
<meta http-equiv="X-UA-Compatible" content="IE=9">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<script src="merchantPages/ResponsiveWebSources/js/<%=lang%>.js"></script>
<script src="merchantPages/ResponsiveWebSources/js/main.js"></script>
<script src="merchantPages/WebSources/js/jquery.js"></script>
<script src="merchantPages/webtech/js/chosen.jquery.js"></script>

<meta name="viewport" content="width=device-width">
<link href="merchantPages/ResponsiveWebSources/css/normalize.css" rel="stylesheet" type="text/css" /> 
<link href="merchantPages/webtech/css/chosen.css" rel="stylesheet" type="text/css" /> 
<link href="merchantPages/webtech/css/style.css" rel="stylesheet" type="text/css" /> 

<title><%=pageTitle%></title>
</head>
<body onload="onLoad();">

<form id="creditForm" onsubmit="return validateFormTrack2CardNo();" method="POST" action="ProcessCreditCard" data-parsley-validate>
	<input type="hidden" name="txId" value="<%=mpiTxnId%>" />
	<input type="hidden" name="lang" value="<%=lang%>" />
	<input type="hidden" name="cardNumber" id="cardNumber" value="" autocomplete="off" />
	<input type="hidden" name="track2" id="track2" value="" autocomplete="off" />
	<input type="hidden" name="last4d" value="" autocomplete="off" />
	<input type="hidden" name="transactionCode" value="Phone" autocomplete="off" />
<!-- 	<input type="hidden" name="userData1" value="" /> -->
	<!--<input type="hidden" name="userData2" value="" />
	<input type="hidden" name="userData3" value="" />
	<input type="hidden" name="userData4" value="" />
	<input type="hidden" name="userData5" value="" />
	<input type="hidden" name="userData6" value="" />
	<input type="hidden" name="userData7" value="" />
	<input type="hidden" name="userData8" value="" />
	<input type="hidden" name="userData9" value="" />
	<input type="hidden" name="userData10" value="" />-->
	
	<div id="amount" class="hide">
		<%=amountForDisplay%>
	</div>


	<div class="form-group">
		<input type="text" class="numericOnly" id="Track2CardNo" name="Track2CardNo" maxlength="80" autocomplete="off" onkeyup="limitInput(this,80)" onchange="return validateTrack2CardNo();" disabled data-parsley-ui-enabled="false" placeholder="מספר כרטיס"/>
		<div class="grid_8 row8 td_style_invalidField" id="invalidCardNumber">&nbsp;</div>
		<div class="grid_8 row8 td_style_invalidField hide" id="invalidTrack2">&nbsp;</div>
	</div>

	<div class="form-group">
	<div class="row">
			
				
			
				<select id="expYear" data-placeholder='שנה' name="expYear" class="form-control chosen-rtl" onchange="validateExpDate();" data-parsley-ui-enabled="false" >
					<%=expYear%>
				</select>
		
			
				<select id="expMonth" data-placeholder='חודש' name="expMonth" class="form-control chosen-rtl" onchange="validateExpDate();" data-parsley-ui-enabled="false" >
					<option value=""></option>
					<option value="01">01</option>
					<option value="02">02</option>
					<option value="03">03</option>
					<option value="04">04</option>
					<option value="05">05</option>
					<option value="06">06</option>
					<option value="07">07</option>
					<option value="08">08</option>
					<option value="09">09</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
				</select>
			
			<input type="text" pattern="[0-9]*" name="cvv" id="cvv" maxlength="4" class="numericOnly" dir="ltr" autocomplete="off" onkeyup="limitInput(this,4)" onchange="return validateCvv();" data-parsley-ui-enabled="false" disabled placeholder="CVV" />
			
			<div>
				<div class="grid_8 row9 td_style_invalidField" id="invalidCardExp">&nbsp;</div>
				<div class="grid_8 row10 td_style_invalidField" id="invalidCvv">&nbsp;</div>
			</div>

			
	</div>

	</div>
	

	<div class="hide">
		<%=getResponsivePaymentsHTML()%>
	</div>		

	<div class="form-group">
		<input type="text" class="numericOnly" pattern="[0-9]*" id="personalId" name="personalId" maxlength="9" autocomplete="off" onkeyup="limitInput(this,9)" onchange="return validatePersonalId()" disabled placeholder="תעודת זהות של בעל הכרטיס" />
		<div class="grid_8 row12 td_style_invalidField" id="invalidPersonalId">&nbsp;</div>
	</div>

  <ul class="creditcard-icons">
		<li>
			<img src="merchantPages/webtech/images/visa.png" />
		</li>
		<li>
			<img src="merchantPages/webtech/images/mastercard.png" />
		</li>
		<li>
			<img src="merchantPages/webtech/images/americanexpress.png" />
		</li>
		<li>
			<img src="merchantPages/webtech/images/isracard.png" />
		</li>
		<li>
			<img src="merchantPages/webtech/images/dinersclub.png" />
		</li>
	</ul>
	
	
	<button type="submit" class="orange-btn" id="submitBtnProxy" value="<%=formSend%>" >שלם</button>
	<input type="submit" id="submitBtn" value="<%=formSend%>" disabled/> 
	<%=getCancelBtnHTML("חזור לסל")%>



	<div id="loading" class="loading_invisible">

	  <div class="spinner">
	    <div class="bounce1"></div>
	    <div class="bounce2"></div>
	    <div class="bounce3"></div>
	  </div>

		<p id="loadingMsg" class="loading_invisible">
			בבדיקה. אנא המתן...
		</p>
		
	</div>
	
</form>

<script type="text/javascript">
	
	helperMsg[2]="CVV לא תקין";

	$('select').chosen({
		disable_search_threshold: 20,
		inherit_select_classes: true

	});

	$('form').on("keyup keypress", function(e) {
	  var code = e.keyCode || e.which; 
	  if (code  == 13) {               
	    e.preventDefault();
	    return false;
	  }
	});

	$('form .numericOnly').on('keyup', function(e){
		this.value = this.value.replace(/\D/g, '');
	});

</script>

</body>

</html>
