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

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import no.bimfm.ifc.InvalidIfcFileException;
import no.bimfm.ifc.RepositoriesImpl;
import no.bimfm.ifc.RepositoryExceptionUc;
import no.bimfm.ifc.v2x3.IfcModelImpl;
import no.bimfm.ifc.v2x3.WholeModelOutput;

import com.sun.jersey.core.header.FormDataContentDisposition;
import com.sun.jersey.multipart.FormDataParam;

@Path("/uploadIfc")
public class UploadIfc {
	private String repositoryName = "temporaryRepository";
	private Logger logger = LoggerFactory.getLogger("no.ifc.rest.UploadIfc");
	@GET
	@Produces(MediaType.TEXT_HTML)
	public String sayHtmlHello() {
	
		String returnData = "You have accept type set to text/html, use POST to upload data";
		return returnData;
	}
	
	@POST
	@Consumes("multipart/form-data")
	@Produces(MediaType.APPLICATION_XML)
	public String uploadFile(@FormDataParam("file") File file, @FormDataParam("file") FormDataContentDisposition fcdsFile,  @FormDataParam("file") InputStream attachmentFile) {
		logger.debug("Upload initiated");
		//String fileLocation = "/files/" + fcdsFile.getFileName();
		String testIfcFileName = "myfile.ifc";
		File destFile = new File(testIfcFileName);
		InputStream in;
		OutputStream out;
		try {
			in = attachmentFile;
			out = new FileOutputStream(destFile);
			byte[] buf = new byte[1024];
			int len;
			while ((len = in.read(buf)) > 0){
				out.write(buf, 0, len);
			}
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
		//String ifcFilename = (Thread.currentThread().getContextClassLoader().get Resource(testIfcFileName)).toString();
		String path  = null;
		try {
			path = new java.io.File(".").getCanonicalPath();
		} catch (IOException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		logger.info("Path to save the file in is {}", path);
		
		RepositoriesImpl repo = new RepositoriesImpl();
		//repo.deleteAllRepositories();
		System.out.println(path + "\\" + testIfcFileName);
		try {
			if(repo.addRepository(this.repositoryName, path + File.separator + testIfcFileName)){
				WholeModelOutput wholeModel= new WholeModelOutput();
				IfcModelImpl model  = new IfcModelImpl(this.repositoryName);
				wholeModel.load(model);
				String result = extractModelXml(wholeModel);
				repo.deleteRepository(this.repositoryName);
				return result;
			} else {
				// should not get in here
				return "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><error><type>repository</type><message>Could not import repository</message></error>";
			}
			
		} catch (InvalidIfcFileException e) {
			logger.error("File was invalid");
			e.printStackTrace();
			return "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><error><type>invalidFile</type><message>Invalid file</message></error>";
		} catch (RepositoryExceptionUc e) {
			logger.error("Error importing");
			e.printStackTrace();
			return "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><error><type>general</type><message>"+e.getMessage()+"</message></error>";
		} finally {
			
			destFile.delete();
		}
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
