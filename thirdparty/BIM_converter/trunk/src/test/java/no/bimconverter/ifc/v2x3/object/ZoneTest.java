package no.bimconverter.ifc.v2x3.object;


import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import no.bimconverter.ifc.IfcTestMethods;
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
		zoneList = model.getZones();
		zone1 = zoneList.get(0);
		
	}
	@After
	public void tearDown() {
	}
	
	
	@Test
	public void testZone1() throws JAXBException {
		//super.outputXmlToSystemOut(zone1);
		Assert.assertNotNull(zone1);
	}

}
