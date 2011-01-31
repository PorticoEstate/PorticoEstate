package no.ifc.rest;


import java.net.URI;

import javax.ws.rs.core.UriBuilder;

import junit.framework.Assert;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

import com.sun.jersey.api.client.Client;
import com.sun.jersey.api.client.WebResource;
import com.sun.jersey.api.client.config.ClientConfig;
import com.sun.jersey.api.client.config.DefaultClientConfig;


public class UploadIfcTest {

	@Before
	public void setUp() throws Exception {
	}

	@After
	public void tearDown() throws Exception {
	}
	
	@Test
	public void testSayHtmlHello() {
		ClientConfig config = new DefaultClientConfig();
		Client client = Client.create(config);
		WebResource service = client.resource(getBaseURI());
		String myString = null;
		String s = service.path("rest").path("uploadIfc").get(String.class);
		Assert.assertEquals("noData", s);
	}
	
	@Test
	public void testSayHtmlHelloClass() {
		UploadIfc test = new UploadIfc();
		Assert.assertEquals("noData", test.sayHtmlHello());
	}
	
	private static URI getBaseURI() {
		return UriBuilder.fromUri("http://localhost:8080/RestTests").build();
	}


}
