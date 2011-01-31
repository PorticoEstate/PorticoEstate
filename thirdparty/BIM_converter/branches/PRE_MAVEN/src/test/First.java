package test;

import java.io.BufferedOutputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URI;

import javax.ws.rs.Consumes;
import javax.ws.rs.FormParam;
import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.PUT;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.core.Context;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.MultivaluedMap;
import javax.ws.rs.core.Request;
import javax.ws.rs.core.Response;
import javax.ws.rs.core.UriInfo;

import jsdai.lang.SdaiException;

import com.sun.jersey.core.header.FormDataContentDisposition;
import com.sun.jersey.multipart.FormDataParam;


import no.bimfm.ifc.RepositoriesImpl;
import no.bimfm.ifc.RepositoryExceptionUc;

// POJO, no interface no extends

//Sets the path to base URL + /hello
@Path("/first")
public class First {
	@Context UriInfo uriInfo;
    @Context Request request;
    
	RepositoriesImpl repo = new RepositoriesImpl();
	// This method is called if TEXT_PLAIN is request
	@GET
	@Produces(MediaType.TEXT_PLAIN)
	public String sayPlainTextHello() {
		RepositoriesImpl repo = new RepositoriesImpl();
		String extra = "";
		//String extra = "non";
		
		return "Hello Jersey "+extra;
	}

	// This method is called if XMLis request
	@GET
	@Produces(MediaType.TEXT_XML)
	public String sayXMLHello() {
		return "<?xml version=\"1.0\"?>" + "<hello> Hello Jersey" + "</hello>";
	}

	// This method is called if HTML is request
	@GET
	@Produces(MediaType.TEXT_HTML)
	public String sayHtmlHello() {
		
		String test = "noData";
		//return "<html> " + "<title>" + "Hello Jersey" + "</title>"
			//	+ "<body><h1>" + "Hello Jersey" + "</body></h1>" +  "</html> ";
		//test = repo.getRepositoryStatus();
		//test = test.replaceAll("\\n", "<br>\n");
		
		return test;
	}
	
	@PUT
	@Consumes(MediaType.TEXT_PLAIN)
	public Response putStuff(String stuff) {
		Response res = null;
		System.out.println("WoooW.. got some data"+stuff);
		URI uri =  uriInfo.getAbsolutePath();
               
        if(stuff.length() > 0)
        	res = Response.created(uri).build();
        else
        	res = Response.noContent().build();
        
		return res;
	}
	
	
	@POST
	@Consumes("multipart/form-data")
	@Produces(MediaType.TEXT_HTML)
	public String uploadFile(@FormDataParam("file") File file, @FormDataParam("file") FormDataContentDisposition fcdsFile, @FormDataParam("repoName") String repoName) {
		
		//String fileLocation = "/files/" + fcdsFile.getFileName();
		String fileLocation = fcdsFile.getFileName();
		
		File destFile = new File(fileLocation);
		// your code here to copy file to destFile
		
		InputStream in;
		OutputStream out;
		try {
			in = new FileInputStream(file);
			out = new FileOutputStream(destFile);
			byte[] buf = new byte[1024];
			int len;
			while ((len = in.read(buf)) > 0){
				out.write(buf, 0, len);
			}
			//file.close();
			in.close();
			out.close();
			System.out.println("Upload success!");
		} catch (FileNotFoundException e) {
			System.out.println("Upload failure (fnf)!");
			e.printStackTrace();
		} catch (IOException e) {
			System.out.println("Upload failure (io)!");
			e.printStackTrace();
		}
		RepositoriesImpl repo = new RepositoriesImpl();
		
		try {
			if(repo.addRepository(repoName, fileLocation)){
				return "Successfully imported!";
			} else {
				// should not get in here
				return "Error importing!";
			}
				
		} catch (RepositoryExceptionUc e) {
			return e.getMessage();
		}
		
		/*
		boolean repoCheck = false;
		try {
			repoCheck = repo.checkIfRepoExists(repoName);
		} catch (SdaiException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		if(repoCheck) {
			return "repo: "+ repoName + " exists!";
		} else {
			return "repo: "+ repoName + " does not exist!";
		}
		
		*/
		
	}
	@POST
	@Consumes("application/x-www-form-urlencoded")
	@Produces(MediaType.TEXT_HTML)
	public String uploadFile2(final MultivaluedMap<String, String>   formParameters) { 
		StringBuilder build = new StringBuilder();
		//String fileLocation = "/files/" + fcdsFile.getFileName();
		for( String s : formParameters.keySet()) {
			build.append(s+";");
		}
		return "Wrong form encoding! Shoud be multipart/form-data";
	}
	@POST
	@Path("first/repond")
	@Consumes("application/x-www-form-urlencoded")
	@Produces(MediaType.TEXT_HTML)
	public String respond(final MultivaluedMap<String, String>   formParameters) { 
		StringBuilder build = new StringBuilder();
		//String fileLocation = "/files/" + fcdsFile.getFileName();
		for( String s : formParameters.keySet()) {
			build.append(s+";");
		}
		return "Wrong form encoding! Shoud be multipart/form-data";
	}	


}
