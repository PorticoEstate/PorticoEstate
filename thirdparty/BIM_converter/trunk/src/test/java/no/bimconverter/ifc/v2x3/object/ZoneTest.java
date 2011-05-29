package no.bimconverter.ifc.v2x3.object;


import static org.junit.Assert.*;

import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import no.bimconverter.ifc.IfcTestMethods;
import no.bimconverter.ifc.jaxb.Attributes;
import no.bimconverter.ifc.jaxb.ZoneAssignment;
import no.bimconverter.ifc.v2x3.object.Zone;
import org.junit.After;
import org.junit.Assert;
import org.junit.Before;
import org.junit.Ignore;
import org.junit.Test;

public class ZoneTest extends IfcTestMethods{	
	private List<Zone> zoneList;
	private Zone zone1;
	
	@Before
	public void setUp() {
		super.createTestRepo();
		zoneList = (List<Zone>) model.getFacilityManagementEntity(new Zone());
		zone1 = zoneList.get(0);
		
	}
	@After
	public void tearDown() {
	}
	
	
	@Test
	public void testZone1() throws JAXBException {
		super.outputXmlToSystemOut(zone1);
		Assert.assertNotNull(zone1);
	}
	
	@Test
	public void testZone1Attributes(){
		Attributes attributes = zone1.getAttributes();
		assertEquals("0h$ksovXH3Jeg0w$Hl1aaf", attributes.getGuid());
		assertEquals("Zone-001", attributes.getName());
		assertEquals("Security zone for entrance", attributes.getDescription());
	}
	
	@Test
	public void testZone1Assigment() {
		ZoneAssignment zoneAssignment = zone1.getZoneAssignment();
		List<String> zoneSpaces = zoneAssignment.getSpaces();
		assertTrue(zoneSpaces.contains("0h$ksovXH3Jeg0w$H7s8af"));
		assertTrue(zoneSpaces.contains("0h$ksovXH3Je2d9dH7s8af"));
		assertTrue(zoneSpaces.contains("0h$ksovXH3Jeg02dH7s8af"));
	}

}
