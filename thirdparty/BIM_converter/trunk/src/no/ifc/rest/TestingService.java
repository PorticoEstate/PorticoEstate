package no.ifc.rest;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URI;

import javax.ws.rs.Consumes;
import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.PUT;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.core.Context;
import javax.ws.rs.core.HttpHeaders;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import javax.ws.rs.core.UriInfo;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import com.sun.jersey.core.header.FormDataContentDisposition;
import com.sun.jersey.multipart.FormDataParam;

/*
 * Used for testing rest service client
 */
@Path("/tests")
public class TestingService {
	private Logger logger = LoggerFactory.getLogger("no.ifc.rest.TestingService");
	
	
	public TestingService() {
		
	}
	@Path("/testPut")
	@POST
	@Consumes("multipart/form-data")
	@Produces(MediaType.TEXT_HTML)
	//public String executePutTest( @Context HttpHeaders headers, InputStream in) {
	public String uploadFile(@FormDataParam("file") InputStream in) {
		logger.info("executePutTest has been called!");
		System.out.println("stuff");
		//URI uri = uriInfo.getAbsolutePath();
        //MediaType mimeType = headers.getMediaType();
        //logger.info("Mediatype is:"+mimeType.getType());
        magic(in);
        
        
        return "Put request success";
	}
	
	private void magic(InputStream in) {
		File destFile = new File("mytestFile.txt");
		try {
			System.out.println(destFile.getCanonicalPath());
		} catch (IOException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		
		// your code here to copy file to destFile
		
		
		OutputStream out;
		try {
			out = new FileOutputStream(destFile);
			byte[] buf = new byte[1024];
			int len;
			while ((len = in.read(buf)) > 0){
				out.write(buf, 0, len);
			}
			//file.close();
			in.close();
			out.close();
			logger.info("Upload success!");
		} catch (FileNotFoundException e) {
			logger.error("Upload failure (fnf)!");
			e.printStackTrace();
		} catch (IOException e) {
			logger.error("Upload failure (io)!");
			e.printStackTrace();
		}
	}
	
}
