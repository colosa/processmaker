<?php
header('Content-Type: text/xml');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"; ?>
<definitions name="InteropTest" targetNamespace="http://soapinterop.org/" 
		xmlns="http://schemas.xmlsoap.org/wsdl/" 
		xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" 
		xmlns:tns="http://soapinterop.org/">

	<import location="http://www.whitemesa.com/wsdl/wmmsgrouter.xsd" namespace="http://whitemesa.com/headers/soapmsgrouter.xsd"/>
	<import location="http://www.whitemesa.com/interop/InteropTest.wsdl" namespace="http://soapinterop.org/"/>
	<import location="http://www.whitemesa.com/interop/InteropTest.wsdl" namespace="http://soapinterop.org/xsd"/>

	<service name="interopLab">
  		<port name="interopTestPort" binding="tns:InteropTestSoapBinding">
			<soap:address location="http://<?php echo $_SERVER["SERVER_NAME"].':'.$_SERVER["SERVER_PORT"];?>/soap_interop/server_Round2Base.php"/>
  		</port>
	</service>

</definitions>
