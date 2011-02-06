package no.bimconverter.ifc.v2x3.object;


import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.v2x3.IfcModelImpl;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class ZoneTest {

	String testingRepository = "FMHandoverRepository";
	String testIfcFileName = "20091007_Test_BasicFM-HandOver_01_valid.ifc";
	
	private IfcModelImpl model;
	private List<Zone> zoneList;
	private Zone zone1;
	
	Repositories repo = null;
	private Map<String,String> baseQuantitiesTestData;
	private Map<String,String> baseQuantitiesCurrentData;
	
	@Before
	public void setUp() {
		model = new IfcModelImpl(testingRepository);
		String ifcFilename = getClass().getResource( "/" +testIfcFileName ).toString();
		
		repo = new RepositoriesImpl();
		repo.addRepository(testingRepository, ifcFilename);
		zoneList = model.getZones();
		zone1 = zoneList.get(0);
		
	}
	@After
	public void tearDown() {
		repo.deleteRepository(testingRepository);
	}
	
	
	@Test
	public void testZone1() throws JAXBException {
		JAXBContext jc = JAXBContext.newInstance(Zone.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( zone1, System.out );
	}

}
