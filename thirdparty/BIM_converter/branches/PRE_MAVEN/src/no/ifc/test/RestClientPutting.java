package no.ifc.test;
import java.net.URI;

import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.UriBuilder;

import com.sun.jersey.api.client.Client;
import com.sun.jersey.api.client.ClientResponse;
import com.sun.jersey.api.client.WebResource;
import com.sun.jersey.api.client.config.ClientConfig;
import com.sun.jersey.api.client.config.DefaultClientConfig;



public class RestClientPutting {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		ClientConfig config = new DefaultClientConfig();
		Client client = Client.create(config);
		WebResource service = client.resource(getBaseURI());
		// Create one todo
		String myTest = "Hola!";
		//myTest = "";
		//ClientResponse response = service.path("rest").path("first").accept(MediaType.T
		String s = service.path("rest").path("first").put(String.class, myTest);
		//String s = service.put(String.class, myTest);
		
		// Return code should be 201 == created resource
		//System.out.println(response.getStatus());
		// Get the Todos


	}
	private static URI getBaseURI() {
		return UriBuilder.fromUri(
				"http://localhost:8080/RestTests").build();
	}


}
