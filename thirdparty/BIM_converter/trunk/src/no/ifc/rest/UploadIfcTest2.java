package no.ifc.rest;


import java.io.UnsupportedEncodingException;
import java.lang.reflect.Field;
import java.util.HashMap;
import java.util.Map;

import org.junit.After;
import org.junit.Assert;
import org.junit.Before;
import org.junit.Test;

import com.sun.jersey.api.client.WebResource;
import com.sun.jersey.server.impl.container.servlet.JerseyServletContainerInitializer;
import com.sun.jersey.test.framework.JerseyTest;
import com.sun.jersey.test.framework.WebAppDescriptor;
import com.sun.jersey.test.framework.spi.container.TestContainerFactory;
import com.sun.jersey.test.framework.spi.container.external.ExternalTestContainerFactory;
import com.sun.jersey.test.framework.spi.container.http.HTTPContainerFactory;
/*
 * To run test in eclipse: 
 * Open run configurations
 * chose arguments tab, and in VM arguments write:
 * -DJERSEY_HTTP_PORT=8080
 */
public class UploadIfcTest2 extends JerseyTest {
	
	public static final String PACKAGE_NAME = "no.ifc.rest";
	public int JERSEY_HTTP_PORT=8080;
	private WebResource ws;
	private String EXPECTED_URI ="http://localhost:8080/RestTests/rest/uploadIfc";
	
	public UploadIfcTest2() {
		super(new WebAppDescriptor.Builder(PACKAGE_NAME).contextPath("RestTests").contextParam("port", "8080").build());
	}
	
	@Before
	public void setUp() {
		
	}
	


	@Test
	public void testService() throws UnsupportedEncodingException {
		ws = resource().path("rest").path("uploadIfc");
		Assert.assertEquals(EXPECTED_URI, ws.getURI().toString());
		
		String result = ws.get(String.class);
		System.out.println(result);
	}
	
	 @Override
	protected TestContainerFactory getTestContainerFactory() {
		 return new ExternalTestContainerFactory();
	}
	 
	

	 
}
