package no.ifc.rest;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.UnsupportedEncodingException;

import javax.ws.rs.Consumes;
import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.MultivaluedMap;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import no.bimfm.ifc.RepositoriesImpl;
import no.bimfm.ifc.RepositoryExceptionUc;
import no.bimfm.ifc.v2x3.IfcModelImpl;
import no.bimfm.ifc.v2x3.WholeModelOutput;

import com.sun.jersey.core.header.FormDataContentDisposition;
import com.sun.jersey.multipart.FormDataParam;

@Path("/uploadIfc")
public class UploadIfc {
	@GET
	@Produces(MediaType.TEXT_HTML)
	public String sayHtmlHello() {
		String test = "noData3";
		return test;
	}
	
	@POST
	@Consumes("multipart/form-data")
	@Produces(MediaType.TEXT_HTML)
	public String uploadFile(@FormDataParam("file") File file, @FormDataParam("file") FormDataContentDisposition fcdsFile,  @FormDataParam("file") InputStream attachmentFile,@FormDataParam("repoName") String repoName) {
		
		//String fileLocation = "/files/" + fcdsFile.getFileName();
		String fileLocation = fcdsFile.getFileName();
		/*try {
			//System.out.println(attachmentFile.available());
		} catch (IOException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}*/
		String testIfcFileName = "myfile.ifc";
		
		File destFile = new File(testIfcFileName);
		
		// your code here to copy file to destFile
		
		InputStream in;
		OutputStream out;
		try {
			in = new FileInputStream(file);
			in = attachmentFile;
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
		//String ifcFilename = (Thread.currentThread().getContextClassLoader().get Resource(testIfcFileName)).toString();
		String path  = null;
		try {
			path = new java.io.File(".").getCanonicalPath();
		} catch (IOException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		System.out.println(path);
		
		RepositoriesImpl repo = new RepositoriesImpl();
		System.out.println(path + "\\" + testIfcFileName);
		try {
			if(repo.addRepository(repoName, path + File.separator + testIfcFileName)){
				WholeModelOutput wholeModel= new WholeModelOutput();
				IfcModelImpl model  = new IfcModelImpl(repoName);
				wholeModel.load(model);
				return extractModelXml(wholeModel);
			} else {
				// should not get in here
				return "Error importing!";
			}
				
		} catch (RepositoryExceptionUc e) {
			return e.getMessage();
		} finally {
			repo.deleteRepository(repoName);
			destFile.delete();
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

	private String extractModelXml(WholeModelOutput wholeModel) {
		ByteArrayOutputStream os = new ByteArrayOutputStream();
		String output = null;
		JAXBContext jc;
		try {
			jc = JAXBContext.newInstance(WholeModelOutput.class);
			Marshaller m = jc.createMarshaller();
			m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
			m.marshal(wholeModel, os );
			output = new String ( os.toByteArray(), "UTF-8");
		} catch (JAXBException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (UnsupportedEncodingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return output;
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
