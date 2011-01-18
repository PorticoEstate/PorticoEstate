package no.bimfm.ifc.v2x3.object;


import static org.junit.Assert.*;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import no.bimfm.ifc.Repositories;
import no.bimfm.ifc.RepositoriesImpl;
import no.bimfm.ifc.v2x3.IfcModelImpl;
import no.bimfm.jaxb.Attributes;
import no.bimfm.jaxb.BaseQuantities;
import no.bimfm.jaxb.Decomposition;
import no.bimfm.jaxb.PropertyList;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class BuildingStoreyTest {

	String testingRepository = "FMHandoverRepository";
	String testIfcFileName = "20091007_Test_BasicFM-HandOver_01_valid.ifc";
	
	private IfcModelImpl model;
	
	private List<BuildingStorey> buildingStoreyList;
	BuildingStorey buildingStorey1;
	BuildingStorey buildingStorey2;
	BuildingStorey buildingStorey3;
	Repositories repo = null;
	
	@Before
	public void setUp() {
		model = new IfcModelImpl(testingRepository);
		String ifcFilename = (Thread.currentThread().getContextClassLoader().getResource(testIfcFileName)).toString();
		ifcFilename = ifcFilename.replace("file:/", "");
		repo = new RepositoriesImpl();
		repo.addRepository(testingRepository, ifcFilename);
		buildingStoreyList = model.getBuildingStoreys();
		buildingStorey1 = buildingStoreyList.get(0);
		buildingStorey2 = buildingStoreyList.get(1);
		buildingStorey3 = buildingStoreyList.get(2);
	}
	@After
	public void tearDown() {
		repo.deleteRepository(testingRepository);
	}
	
	@Test
	public void testCorrectNumberOfStoreys() {
		assertEquals(3, buildingStoreyList.size());
	}
	
	@Test
	public void testFirstStorey() throws JAXBException {
		
		//this.testFirstStoreyAttributes();
		JAXBContext jc = JAXBContext.newInstance(BuildingStorey.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( buildingStorey1, System.out );
	}
	@Test
	public void testSecondStorey() throws JAXBException {
		
		//this.testFirstStoreyAttributes();
		JAXBContext jc = JAXBContext.newInstance(BuildingStorey.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( buildingStorey2, System.out );
	}
	@Test
	public void testThirdStorey() throws JAXBException {
		
		//this.testFirstStoreyAttributes();
		JAXBContext jc = JAXBContext.newInstance(BuildingStorey.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( buildingStorey3, System.out );
	}
	
	@Test
	public void testFirstStoreyAttributes() {
		Attributes attributes = buildingStorey1.getAttributes();
		assertEquals("0h$ksovXH3Jeg0w$H7aaaf", attributes.getGuid());
		assertEquals("A.-1", attributes.getName());
		assertEquals("Kellergeschoss", attributes.getDescription());
		assertEquals("KG", attributes.getLongName());
		assertEquals("-3.2", attributes.getElevation());
	}
	@Test
	public void testSecondStoreyAttributes() {
		Attributes attributes = buildingStorey2.getAttributes();
		assertEquals("0h$ksovXH3Jeg0w$H7yFJf", attributes.getGuid());
		assertEquals("A.0", attributes.getName());
		assertEquals(null, attributes.getDescription());
		assertEquals("EG", attributes.getLongName());
		assertEquals("0.0", attributes.getElevation());
	}
	
	@Test
	public void testThirdStoreyAttributes() {
		Attributes attributes = buildingStorey3.getAttributes();
		assertEquals("0h$ksovXH3Jee5w$H7yFJf", attributes.getGuid());
		assertEquals("A.+1", attributes.getName());
		assertEquals(null, attributes.getDescription());
		assertEquals("1.OG", attributes.getLongName());
		assertEquals("3.2", attributes.getElevation());
	}
	@Test
	public void testFirstStoreyBaseQuantities() {
		BaseQuantities attributes = buildingStorey1.getBaseQuantities();
		assertNull(attributes);
	}
	@Test
	public void testSecondStoreyBaseQuantities() {
		BaseQuantities attributes = buildingStorey2.getBaseQuantities();
		this.testBaseQuantities(attributes.getElementMap());
		
	}
	@Test
	public void testThirdStoreyBaseQuantities() {
		BaseQuantities attributes = buildingStorey3.getBaseQuantities();
		this.testBaseQuantities(attributes.getElementMap());
		
	}
	
	private void testBaseQuantities(Map<String, String> map) {
		Map<String,String> testData = new HashMap<String, String>();
		testData.put("GrossHeight", "3.2");
		testData.put("NetHeight", "3.0");
		assertEquals(testData, map);
		
		/*for(NameValuePair item : map) {
			if(item.name.equals("GrossHeight")) {
				assertEquals("3.2", item.value);
			} else if(item.name.equals("NetHeight")) {
				assertEquals("3.0", item.value);
			}
		}*/
	}
	
	@Test
	public void testFirstStoreyProperties() {
		assertNull(buildingStorey1.getProperties());
	}
	
	@Test
	public void testSecondStoreyProperties() {
		List<PropertyList> properties = buildingStorey2.getProperties();
		for(PropertyList propertyList : properties) {
			assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), propertyList.getName());
			Map<String, String> propertyMap = propertyList.getElementMap();
			assertEquals(2, propertyMap.size());
			assertEquals("IFCBOOLEAN(.T.)", propertyMap.get("EntranceLevel"));
			assertEquals("IFCLOGICAL(.T.)", propertyMap.get("AboveGround"));
		}
		
	}
	
	@Test
	public void testThirdStoreyProperties() {
		assertNull(buildingStorey3.getProperties());
	}
	
	@Test
	public void testSpatialCompositoin() {
		this.testSpatialComposition(buildingStorey1);
		this.testSpatialComposition(buildingStorey2);
		this.testSpatialComposition(buildingStorey3);
	}
	
	public  void testSpatialComposition(BuildingStorey buildingStorey) {
		Decomposition spatialComposition = buildingStorey.getSpatialDecomposition();
		//String[] projectId = spatialComposition.get(BuildingStorey.SpatialDecomposition.PROJECT.key);
		assertNotNull(spatialComposition.getProject());
		//String[] siteId = spatialComposition.get(BuildingStorey.SpatialDecomposition.SITE.key);
		
		assertNotNull(spatialComposition.getSite());
		assertEquals("28hfXoRX9EMhvGvGhmaaad", spatialComposition.getSite() );	
		List<String> buildingList = spatialComposition.getBuildings();
		assertEquals(buildingList.size(), 1);
		assertEquals("28hfXoRX9EMhvGvGhmaaae", buildingList.get(0) );	
	}
	
	

}
