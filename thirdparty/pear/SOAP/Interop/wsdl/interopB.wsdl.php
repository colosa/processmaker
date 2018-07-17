<?php
header('Content-Type: text/xml');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<definitions name="InteropTest" targetNamespace="http://soapinterop.org/" xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="http://soapinterop.org/" xmlns:s="http://soapinterop.org/xsd" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">

	<import location="http://www.whitemesa.com/interop/InteropTestB.wsdl" namespace="http://soapinterop.org/"/>
	<import location="http://www.whitemesa.com/interop/InteropTestB.wsdl" namespace="http://soapinterop.org/xsd"/>

	<service name="interopLabB">
  		<port name="interopTestPortB" binding="tns:InteropTestSoapBindingB">
			<soap:address location="http://<?php echo $_SERVER["SERVER_NAME"].':'.$_SERVER["SERVER_PORT"];?>/soap_interop/server_Round2GroupB.php"/>
  		</port>
	</service>

</definitions>
