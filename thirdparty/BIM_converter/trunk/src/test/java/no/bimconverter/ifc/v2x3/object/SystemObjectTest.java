package no.bimconverter.ifc.v2x3.object;


import static org.junit.Assert.*;

import java.util.List;

import no.bimconverter.ifc.IfcTestMethods;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class SystemObjectTest extends IfcTestMethods{	
	
	List<SystemObject> systemList;
	
	@Before
	public void setUp() {
		super.createTestRepo();
		systemList = (List<SystemObject>) model.getFacilityManagementEntity(new SystemObject());
	}

	@After
	public void tearDown() throws Exception {
	}
	
	@Test
	public void testInit() {
		assertNotNull(systemList);
		assertEquals(3, systemList.size());
	}
	
	@Test
	public void displaySystems() {
		for(SystemObject so : this.systemList) {
			super.outputXmlToSystemOut(so);
		}
	}

}
