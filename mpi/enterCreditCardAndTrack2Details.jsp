<%@ page language="java" contentType="text/html; charset=utf-8"
	pageEncoding="utf-8" errorPage="/error.jsp"%>
<jsp:useBean id="transactionDetails" scope="request" type="com.creditguard.common.transactions.TransactionDetails" />
<%@ page import="java.text.DecimalFormat" %>
<%@ include file="/merchantPages/WebSources/includes/main.jsp" %>

<%! DecimalFormat formatter = new DecimalFormat("0.00"); %>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<script src="merchantPages/WebSources/js/<%=lang%>.js"></script>
<script src="merchantPages/WebSources/js/main.js"></script>
<link href="merchantPages/WebSources/css/main.rtl.css" rel="stylesheet" type="text/css" />
<%=engCSS%>

<title><%=pageTitle%></title>
</head>
<body onload="onLoad();">

<!--  DISPLAY TRANSACTION INFORMATION -->
<div id="main_frame" align="center">
<div id="main">
	<div id="page_title"><img id="ssl_logo" src="merchantPages/WebSources/images/ssl.png" alt="" title=""/><h1><%=pageTitle%></h1></div>
	<div id="logo"><img src="merchantPages/WebSources/images/logo_b.png" alt="" title=""/></div>
	<div class="sep"></div>
	<div id="page_desc">
		<em><%=pageDesc%></em>
	</div>
	<form id="creditForm" onsubmit="return validateFormTrack2CardNo();" method="POST" action="ProcessCreditCard">
	<input type="hidden" name="txId" value="<%=mpiTxnId%>"/>
	<input type="hidden" name="lang" value="<%=lang%>"/>
	<input type="hidden" name="cardNumber" id="cardNumber" value="" autocomplete="off" />
	<input type="hidden" name="track2" id="track2" value="" autocomplete="off" />
	<input type="hidden" name="last4d" value="" autocomplete="off" />
	<input type="hidden" name="transactionCode" value="Phone" autocomplete="off" />
	<!--<input type="hidden" name="userData1" value=""/>
	<input type="hidden" name="userData2" value=""/>
	<input type="hidden" name="userData3" value=""/>
	<input type="hidden" name="userData4" value=""/>
	<input type="hidden" name="userData5" value=""/>
	<input type="hidden" name="userData6" value=""/>
	<input type="hidden" name="userData7" value=""/>
	<input type="hidden" name="userData8" value=""/>
	<input type="hidden" name="userData9" value=""/>
	<input type="hidden" name="userData10" value=""/>-->
	
	<table class="data_tbl" border=0>
		<thead>
			<tr>
				<th colspan="4" class="td_style_1"><%=paymentDetails%></th>
			</tr>
		</thead>
		<tr>
			<td class="td_style_fieldName"><%=paymentAmount%>:</td>
			<td id="amount" style="width: 205px;"><%=amountForDisplay%></td>
			<td class="td_style_fieldName"><%=paymentCEmail%>:</td>
			<td style="width: 145px;"><%=customerEmail%></td>
		</tr>
		<tr>
			<td class="td_style_fieldName"><%=paymentCurrency%>:</td>
			<td><%=currency%></td>
			<td class="td_style_fieldName"><%=paymentOrderId%>:</td>
			<td><%=orderId%></td>
		</tr>
		<tr>
			<td class="td_style_fieldName"><%=paymentDesc%>:</td>
			<td><%=description%></td>
		</tr>
	</table>
	<table class="data_tbl" border=0>
		<thead>
			<tr>
				<th colspan="4" class="td_style_1"><%=CCDetails%></th>
			</tr>
		</thead>
		<tr id="tr_cardNumber">
			<td class="td_style_fieldName"><%=CCNumber%>:</td>
			<td>
				<input type="text" id="Track2CardNo" name="Track2CardNo" maxlength="80" style="width: 160px;" autocomplete="off" onchange="return validateTrack2CardNo();" tabindex="1"/>
			</td>
			<td class="td_style_invalidField" id="invalidCardNumber" >&nbsp;</td>
			<td class="td_style_invalidField" id="invalidTrack2" >&nbsp;</td>
		</tr>
		<tr>
			<td class="td_style_fieldName"><%=CCExp%>:</td>
			<td>
				<select id="expYear" name="expYear" onchange="validateExpDate();" disabled tabindex="2">
					<%=expYear%>
				</select>
				-
				<select id="expMonth" name="expMonth" onchange="validateExpDate();" disabled tabindex="3">
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
			</td>
			<td colspan="2" class="td_style_invalidField" id="invalidCardExp">&nbsp;</td>
		</tr>
		<tr>
			<td class="td_style_fieldName">CVV:</td>
			<td><input type="text" name="cvv" id="cvv" maxlength="4" style="width: 50px;" autocomplete="off" disabled tabindex="4"/> <img src="merchantPages/WebSources/images/qm.png"  onmouseover="showHideCVVhelp();" onmouseout="showHideCVVhelp();" style="cursor:pointer;"/><div id="CVVhelp" style="display: none; position: absolute; border: 1px #cccccc solid; padding: 10px; background: white;"><img src="merchantPages/WebSources/images/cvv.jpg" /></div></td>
			<td colspan="2" class="td_style_invalidField" id="invalidCvv">&nbsp;</td>
		</tr>
		<!-- <tr>
			<td class="td_style_fieldName"><%=cvv_wneb%>:</td>
			<td>
				<select id="cvv_wneb" name="cvv_wneb" onchange="cvv_wneb_changed();" disabled >
					<option value=""></option>
					<option value="0"><%=cvv_wneb_0%></option>
					<option value="2"><%=cvv_wneb_2%></option>
					<option value="9"><%=cvv_wneb_9%></option>
				</select>
			</td>
			<td class="td_style_fieldName"></td>
		</tr> -->
		<tr>
			<td class="td_style_fieldName"><%=CCPId%>:</td>
			<td><input type="text" id="personalId" name="personalId" maxlength="9" style="width: 145px;" autocomplete="off" disabled tabindex="5"/></td>
			<td class="td_style_invalidField" id="invalidPersonalId">&nbsp;</td>
		</tr>
		<%=getPaymentsHTML()%>
	</table>
	<div id="form_buttons">
		<input type="submit" id="submitBtn" value="<%=formSend%>" disabled tabindex="6" /> 
		<input id="resetBtn" type="reset" value="<%=formReset%>" disabled tabindex="7" onclick="onClear();"/> 
		<%=getCancelBtnHTML("")%>
	</div>
	</form>
	<div id="loading" class="loading_invisible"><p id="loadingMsg" class="loading_invisible"></p></div>
</div>
<br/>
<table border=0>
<tr>
<td align="right">
<!-- GeoTrust True Site [tm] Smart Icon tag. Do not edit. -->
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript"
SRC="//smarticon.geotrust.com/si.js"></SCRIPT>
<!-- end GeoTrust Smart Icon tag -->
</td>
<td align="left">
<img src="merchantPages/WebSources/images/pci.png" alt="" title=""/>
</td>
</tr>
</table>
</div>
</body>
</html>