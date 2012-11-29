// Main class to sent differen kind of messages to the http server
import org.apache.http.impl.client.DefaultHttpClient;

// Enter CRUD memebers 
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.methods.HttpDelete;

// Used to set JSON or XML messages request
import org.apache.http.entity.StringEntity;

// Needed for response fetch goal
import org.apache.http.HttpResponse;
import org.apache.http.HttpEntity;
import org.apache.http.util.EntityUtils;

/**
* Single class containing functions to show how to use GET,POST,PUT,DELETE methods.
*/
public class CRUD
{
    private static String m_user = "workflow"; // This member variable must be changed to its own dev workspace

    private static void PostSample()
    {
        System.out.println("POST: Enter login params\n");

        String loginParamsXML = "<?xml version='1.0'?>\n"
                +"<request>\n"
                +"<user>admin</user>\n"
                +"<password>admin</password>\n"
                +"</request>";
        String URI = "http://"+m_user+".pmos.colosa.net/rest/"+m_user+"/login/";

        System.out.println( "Request: "+URI + "\n"+ loginParamsXML + "\n");

        DefaultHttpClient httpClient = new DefaultHttpClient();
        HttpPost postRequest = new HttpPost(URI);
        try
        {
            StringEntity input = new StringEntity( loginParamsXML);
            input.setContentType("application/xml");
            postRequest.setEntity(input);
            HttpResponse httpResponse = httpClient.execute(postRequest);
            
            HttpEntity responseEntity = httpResponse.getEntity();
            if( responseEntity != null)
            {
                String response = new String();
                response = EntityUtils.toString( responseEntity);
                System.out.println( "Response: " + response + "\n");
            }
        }
        catch( java.io.IOException x)
        {
            throw new RuntimeException("I/O error: " + x.toString()); 
        }
    }
    
    private static void GetSample()
    {
        System.out.println("GET: Display TRANSLATION table row\n");

        String URI = "http://"+m_user+".pmos.colosa.net/rest/"+m_user+"/TRANSLATION/LABEL/LOGIN/en/";
        System.out.println( "Request: " + URI + "\n");

        DefaultHttpClient httpClient = new DefaultHttpClient();
        HttpGet getRequest = new HttpGet(URI);
        try
        {
            HttpResponse httpResponse = httpClient.execute(getRequest);
            
            HttpEntity responseEntity = httpResponse.getEntity();
            if( responseEntity != null)
            {
                String response = new String();
                response = EntityUtils.toString( responseEntity);
                System.out.println( "Response: " + response + "\n");
            }
        }
        catch( java.io.IOException x)
        {
            throw new RuntimeException("I/O error: " + x.toString()); 
        }
    }

    private static void AnotherPostSample()
    {
        System.out.println("POST: Insert new row in TRANLATION\n");

        String URI = "http://"+m_user+".pmos.colosa.net/rest/"+m_user+"/TRANSLATION/";
        String newRow = "BUTTON/ESCAPE/en/sample/2012-05-05/";
        System.out.println( "Request: " + URI + " new row: " + newRow + "\n");
        URI = URI + newRow;

        DefaultHttpClient httpClient = new DefaultHttpClient();
        HttpPost postRequest = new HttpPost(URI);
        try
        {
            HttpResponse httpResponse = httpClient.execute(postRequest);
            
            HttpEntity responseEntity = httpResponse.getEntity();
            if( responseEntity != null)
            {
                String response = new String();
                if(response.isEmpty())
                {
                    System.out.println( "Response: Status code: " + httpResponse.getStatusLine().getStatusCode()+ "\n");
                    return;
                }
                response = EntityUtils.toString( responseEntity);
                System.out.println( "Response: " + response + "\n");
            }
        }
        catch( java.io.IOException x)
        {
            throw new RuntimeException("I/O error: " + x.toString()); 
        }
    }

    private static void PutSample()
    {
        System.out.println("POST: Update a row in TRANLATION\n");

        String URI = "http://"+m_user+".pmos.colosa.net/rest/"+m_user+"/TRANSLATION/";
        String index = "BUTTON/ESCAPE/en/";
        String updateData = "changesample/2011-07-06/";

        System.out.println( "Request: " + URI + " index: " + index + " updateData: " + updateData + "\n");
        URI = URI + index + updateData;

        DefaultHttpClient httpClient = new DefaultHttpClient();
        HttpPut putRequest = new HttpPut(URI);
        try
        {
            HttpResponse httpResponse = httpClient.execute(putRequest);
            
            HttpEntity responseEntity = httpResponse.getEntity();
            if( responseEntity != null)
            {
                String response = new String();
                if(response.isEmpty())
                {
                    System.out.println( "Response: Status code: " + httpResponse.getStatusLine().getStatusCode()+ "\n");
                    return;
                }
                response = EntityUtils.toString( responseEntity);
                System.out.println( "Response: " + response + "\n");
            }
        }
        catch( java.io.IOException x)
        {
            throw new RuntimeException("I/O error: " + x.toString()); 
        }
    }

    private static void DeleteSample()
    {
        System.out.println("DELETE: Remove a row in TRANLATION\n");

        String URI = "http://"+m_user+".pmos.colosa.net/rest/"+m_user+"/TRANSLATION/";
        String index = "BUTTON/ESCAPE/en/";

        System.out.println( "Request: " + URI + "index:" + index + "\n");
        URI = URI + index;

        DefaultHttpClient httpClient = new DefaultHttpClient();
        HttpDelete deleteRequest = new HttpDelete(URI);
        try
        {
            HttpResponse httpResponse = httpClient.execute(deleteRequest);
            
            HttpEntity responseEntity = httpResponse.getEntity();
            if( responseEntity != null)
            {
                String response = new String();
                if(response.isEmpty())
                {
                    System.out.println( "Response: Status code: " + httpResponse.getStatusLine().getStatusCode()+ "\n");
                    return;
                }
                response = EntityUtils.toString( responseEntity);
                System.out.println( "Response: " + response + "\n");
            }
        }
        catch( java.io.IOException x)
        {
            throw new RuntimeException("I/O error: " + x.toString()); 
        }
    }

    public static void main(String args[])
    {
        System.out.println("CRUD samples.");
        PostSample();
        GetSample();
        AnotherPostSample();
        PutSample();
        DeleteSample();
	
    }
} 