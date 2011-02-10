package no.bimconverter.ifc.v2x3.object;



import static junit.framework.Assert.*;
import static org.junit.Assert.assertEquals;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import junit.framework.Assert;



import no.bimconverter.ifc.IfcTestMethods;
import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.jaxb.Attributes;
import no.bimconverter.ifc.jaxb.Decomposition;
import no.bimconverter.ifc.jaxb.PropertyList;
import no.bimconverter.ifc.v2x3.IfcModelImpl;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class BuildingTest extends IfcTestMethods{
	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	String ifcFilename = null;
	
	private List<Building> buildingsList;
	Building building;

	@Before
	public void setUp() {
		super.createTestRepo();
		buildingsList = model.getBuildings();
		building = buildingsList.get(0);
	}
	@After
	public void tearDown() {
	}
	
	@Test
	public void testCreateAndIntializeBuildingObject() throws JAXBException {
		Building building = buildingsList.get(0);
		
		JAXBContext jc = JAXBContext.newInstance(Building.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( building, System.out );
		
		 
		assertNotNull(buildingsList);
	}
	@Test
	public void checkThatThereIsOneBuilding() {
		assertTrue(buildingsList.size() == 1);
	}
	@Test
	public void testBuildingAttributes() {
		Attributes attributes = building.getAttributes();
		assertEquals("28hfXoRX9EMhvGvGhmaaae", attributes.getGuid());
		assertEquals("01.1.A", attributes.getName());
		assertEquals("Building object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("FM Test Building A", attributes.getLongName());
	}
	@Test
	public void testAddressIsEmpty() {
		assertNull(building.getAddress());
	}
	
	@Test
	public void testSpatialDecomposition() {
		Decomposition spatialComposition = building.getSpatialDecomposition();
		//String[] project = spatialComposition.get(BuildingStorey.SpatialDecomposition.PROJECT.key);
		assertNotNull(spatialComposition.getProject());
		assertEquals("3KFKb0sfrDJwSHalGIQFZT", spatialComposition.getProject() );
		//String[] site = spatialComposition.get(BuildingStorey.SpatialDecomposition.SITE.key);
		assertNotNull(spatialComposition.getSite());
		assertEquals("28hfXoRX9EMhvGvGhmaaad", spatialComposition.getSite() );
		//String[] storeys = spatialComposition.get(BuildingStorey.SpatialDecomposition.STOREY.key);
		List<String> storeys = spatialComposition.getBuildingStoreys();
		assertEquals(storeys.size(), 3);
		// add checks for each storey uid
		assertEquals("0h$ksovXH3Jeg0w$H7aaaf", storeys.get(0) );
		assertEquals("0h$ksovXH3Jeg0w$H7yFJf", storeys.get(1) );
		assertEquals("0h$ksovXH3Jee5w$H7yFJf", storeys.get(2) );
	}
	
	@Test
	public void testProperties() {
		
		List<PropertyList> properties = building.getProperties();
		for(PropertyList propertyList : properties) {
			assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), propertyList.getName());
			Map<String, String> propertyMap = propertyList.getElementMap();
			assertEquals(2, propertyMap.size());
			assertEquals("IFCLOGICAL(.F.)", propertyMap.get("IsLandmarked"));
			assertEquals("IFCLABEL('2002')", propertyMap.get("YearOfConstruction"));
			/*
			int tests = 0;
			for(int i = 0; i < propertyList.getProperties().size(); i++) {
				NameValuePair nvp = propertyList.getProperties().get(i);
				if(nvp.name.equals("IsLandmarked")) {
					assertEquals("IFCLOGICAL(.F.)", nvp.value);
					tests++;
				}else if(nvp.name.equals("YearOfConstruction")) {
					assertEquals("IFCLABEL('2002')", nvp.value);
					tests++;
				}
			}
			assertEquals(2, tests);
			*/
		}
		/*
		List<XEntry<String, XMap<String, String>>> list = properties.getList();
		for(XEntry<String, XMap<String, String>> entry : list) {
			assertEquals("Common property", entry.getKey());
			 XMap<String, String> value = entry.getValue();
			 List<XEntry<String, String>> innerList = value.getList();
			 Map<String, String> dataMap = new HashMap<String, String>();
			 for(XEntry<String, String> innerListEntry: innerList) {
				 dataMap.put(innerListEntry.getKey(), innerListEntry.getValue());
			 }
			 assertEquals("IFCLOGICAL(.F.)", dataMap.get("IsLandmarked"));
			 assertEquals("IFCLABEL('2002')", dataMap.get("YearOfConstruction"));
		}
		*/
	}
	
}
