<%@ page language="java" contentType="text/html; charset=utf-8"
	pageEncoding="utf-8" errorPage="/error.jsp"%>

<%@ include file="/merchantPages/WebSources/includes/ok.jsp" %>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="-1">
<link href="/CGMPI_Server/merchantPages/WebSources/css/main.rtl.css" rel="stylesheet" type="text/css" />
<link href="merchantPages/WebSources/css/main.ltr.css" rel="stylesheet" type="text/css" />

<title>טראנזקציה התבצעה בהצלחה</title>
</head>
<body>
<!--  DISPLAY TRANSACTION OK INFORMATION -->
<div id="main_frame" align="center">
<div id="main">
	<div id="page_title"><img id="ssl_logo" src="/CGMPI_Server/merchantPages/WebSources/images/ssl.png" alt="" title=""/><h1><%=ok_title%></h1></div>
	<div id="logo"><img src="/CGMPI_Server/merchantPages/WebSources/images/logo_b.png" alt="" title=""/></div>
	<div class="sep"></div>
	<div id="page_desc">
		מספר טראנזקציה: <strong><%=tid%></strong>
	</div>
</div>
<br />

<table border=0>
<tr>
<td align="right">
<!-- GeoTrust True Site [tm] Smart Icon tag. Do not edit. -->
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript"
SRC="//smarticon.geotrust.com/si.js"></SCRIPT>
<!-- end GeoTrust Smart Icon tag -->
</td>
<td align="left">
<img src="/CGMPI_Server/merchantPages/WebSources/images/pci.png" alt="" title=""/>
</td>
</tr>
</table>
</div>
</body>

</html>

