package no.bimfm.ifc;




import static org.junit.Assert.*;

import jsdai.lang.SdaiException;
import jsdai.lang.SdaiSession;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class IfcSdaiRepresentationImplTest {

	@Before
	public void setUp() throws Exception {
	}

	@After
	public void tearDown() throws Exception {
		SdaiSession session = SdaiSession.getSession();
		if(session != null)
			session.closeSession();	
	}
	
	@Test
	public void testCreateInstance() {
		IfcSdaiRepresentation isr = new IfcSdaiRepresentationImpl();
		assertTrue(isr != null);
	}
	@Test(expected=IfcSdaiException.class)
	public void testFailIfNoFile() {
		IfcSdaiRepresentation isr = new IfcSdaiRepresentationImpl("bogus");
	}
	@Test(expected=IfcSdaiException.class)
	public void testFailIfSessionOpen() throws SdaiException {
		SdaiSession.openSession();
		IfcSdaiRepresentation isr = new IfcSdaiRepresentationImpl();
	}
	
	
	

}
