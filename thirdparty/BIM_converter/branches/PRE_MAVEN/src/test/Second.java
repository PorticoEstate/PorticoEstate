package test;


import java.util.List;

import javax.ws.rs.Consumes;
import javax.ws.rs.POST;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.core.Context;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.MultivaluedMap;
import javax.ws.rs.core.Request;
import javax.ws.rs.core.UriInfo;

import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiModel;

import no.bimfm.ifc.RepositoriesImpl;
import no.bimfm.ifc.RepositoryException;

// POJO, no interface no extends

//Sets the path to base URL + /hello
@Path("/second")
public class Second {
	@Context UriInfo uriInfo;
    @Context Request request;
    
	RepositoriesImpl repo = new RepositoriesImpl();
	
	@POST
	@Consumes("application/x-www-form-urlencoded")
	@Produces(MediaType.TEXT_HTML)
	public String respond(final MultivaluedMap<String, String>   formParameters) { 
		StringBuilder build = new StringBuilder();
		//String fileLocation = "/files/" + fcdsFile.getFileName();
		for( String s : formParameters.keySet()) {
			build.append(s+";");
			build.append(formParameters.get(s)+"\n<br>");
		}
		List<String> yo = formParameters.get("repoName");
		List<EEntity> model = null; //repo.getModel(yo.get(0));
		if ( model != null) {
			System.out.println("Got the model!!!!");
			System.out.println("It has this many beautiful instances:"+model.size());
		}
		
		return "data:"+yo.get(0);
	}	


}
