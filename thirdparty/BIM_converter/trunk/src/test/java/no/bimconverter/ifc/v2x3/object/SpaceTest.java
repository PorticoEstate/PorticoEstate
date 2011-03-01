package no.bimconverter.ifc.v2x3.object;


import static org.junit.Assert.*;


import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import jsdai.SIfc2x3.EIfcinternalorexternalenum;


import no.bimconverter.ifc.IfcTestMethods;
import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.jaxb.Attributes;
import no.bimconverter.ifc.jaxb.BoundaryItem;
import no.bimconverter.ifc.jaxb.ClassificationItem;
import no.bimconverter.ifc.jaxb.Decomposition;
import no.bimconverter.ifc.jaxb.PropertyList;
import no.bimconverter.ifc.jaxb.SpaceSpatialContainer;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

/*
 * TODO: complete testSpaceXSpatialContainer tests
 */
public class SpaceTest extends IfcTestMethods{

	private List<Space> spaceList;
	private Space space1;
	private Space space2;
	private Space space3;
	private Space space4;
	private Space space5;
	private Space space6;
	private Space space7;
	private Space space8;
	
	
	private Map<String,String> baseQuantitiesTestData;
	private Map<String,String> baseQuantitiesCurrentData;
	
	@Before
	public void setUp() {
		super.createTestRepo();
		spaceList = model.getSpaces();
		space1 = spaceList.get(0);
		space2 = spaceList.get(1);
		space3 = spaceList.get(2);
		space4 = spaceList.get(3);
		space5 = spaceList.get(4);
		space6 = spaceList.get(5);
		space7 = spaceList.get(6);
		space8 = spaceList.get(7);
		
	}
	@After
	public void tearDown() {
	}
	
	@Test
	public void testCorrectNumberOfStoreys() {
		assertEquals(8, spaceList.size());
	}
	private void compareBaseQuantities() {
		assertEquals(this.baseQuantitiesTestData,this.baseQuantitiesCurrentData);
	}
	@Test
	public void testSpace1() throws JAXBException {
		//super.outputXmlToSystemOut(space1);
	}
	@Test
	public void testSpace1Attributes(){
		Attributes attributes = space1.getAttributes();
		assertEquals("0h$ksovXH3Jeg0w$H724af", attributes.getGuid());
		assertEquals("EG.001", attributes.getName());
		assertEquals("Space-001 object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("Main function room", attributes.getLongName());
		assertEquals(EIfcinternalorexternalenum.toString(EIfcinternalorexternalenum.INTERNAL), attributes.getInternalExternal());
	}
	@Test
	public void testSpace1Classifications() {
		List<ClassificationItem> classifications = space1.getClassificationList();
		ClassificationItem item1 = classifications.get(0);
		assertTrue(classifications.size() == 1);
		assertEquals("1.5", item1.itemKey);
		assertEquals("Speiseräume", item1.itemName);
		assertEquals("DIN277-2", item1.systemName);
		assertEquals("2005", item1.systemEdition);
	}
	@Test
	public void testSpace1BaseQuantities() {
		this.baseQuantitiesCurrentData = space1.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("GrossCeilingArea", "125.44");
		this.baseQuantitiesTestData.put("NetCeilingArea", "125.4");
		this.baseQuantitiesTestData.put("NetWallArea", "130.3");
		this.baseQuantitiesTestData.put("NetFloorArea", "125.4");
		this.baseQuantitiesTestData.put("FinishCeilingHeight", "2.9");
		this.baseQuantitiesTestData.put("GrossWallArea", "135.6");
		this.baseQuantitiesTestData.put("GrossPerimeter", "45.2");
		this.baseQuantitiesTestData.put("GrossFloorArea", "125.44");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = space1.getBaseQuantities();
		assertEquals(8, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("GrossCeilingArea")) {
				assertEquals("125.44", item.value);
				testCount++;
			} else if(item.name.equals("NetCeilingArea")) {
				assertEquals("125.4", item.value);
				testCount++;
			}else if(item.name.equals("NetWallArea")) {
				assertEquals("130.3", item.value);
				testCount++;
			}
			else if(item.name.equals("NetFloorArea")) {
				assertEquals("125.4", item.value);
				testCount++;
			}
			else if(item.name.equals("FinishCeilingHeight")) {
				assertEquals("2.9", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossWallArea")) {
				assertEquals("135.6", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossPerimeter")) {
				assertEquals("45.2", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossFloorArea")) {
				assertEquals("125.44", item.value);
				testCount++;
			}
		}
		assertEquals(8, testCount);*/
	}
	@Test
	public void testSpace1Properties() {
		List<PropertyList> spaceProperties = space1.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		assertEquals(3, propertyMap.size());
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("CeilingCovering", "IFCLABEL('Paint')");
		currentProperties.put("WallCovering", "IFCLABEL('Paint')");
		currentProperties.put("FloorCovering", "IFCLABEL('Parquet')");
		assertEquals(currentProperties, propertyMap);
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("CeilingCovering")) {
				assertEquals("IFCLABEL('Paint')", nvp.value);
				testCount++;
			} else if (nvp.name.equals("WallCovering")) {
				assertEquals("IFCLABEL('Paint')", nvp.value);
				testCount++;
			}else if (nvp.name.equals("FloorCovering")) {
				assertEquals("IFCLABEL('Parquet')", nvp.value);
				testCount++;
			}
		}
		assertEquals(3, testCount);*/
	}
	
	@Test
	public void testSpace1SpatialContainer() {
		SpaceSpatialContainer spatialContainer = space1.getSpatialContainer();
		assertNull(spatialContainer.getCoverings());
		assertNull(spatialContainer.getDoorsWindows());
		assertEquals(3, spatialContainer.getElements().size());
	}
	@Test
	public void testSpace1Boundary() {
		List<BoundaryItem> boundary = space1.getSpaceBoundary();
		assertEquals(1, boundary.size());
		BoundaryItem item1 = boundary.get(0);
		BoundaryItem referenceItemn = new BoundaryItem("2cs23eea03h3g387ZsghoF", "ifcdoor", "Physical", "Internal");
		assertEquals(referenceItemn, item1);
	}
	@Test
	public void testSpace2() throws JAXBException {
		//super.outputXmlToSystemOut(space3);
	}
	@Test
	public void testSpace2Attributes(){
		Attributes attributes = space2.getAttributes();
		assertEquals("0h$ksovXH3Jeg0w$H7s8af", attributes.getGuid());
		assertEquals("EG.002", attributes.getName());
		assertEquals("Space-002 object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("Preparation room", attributes.getLongName());
		assertEquals(EIfcinternalorexternalenum.toString(EIfcinternalorexternalenum.NOTDEFINED), attributes.getInternalExternal());
	}
	@Test
	public void testSpace2Classifications() {
		List<ClassificationItem> classifications = space2.getClassificationList();
		ClassificationItem item1 = classifications.get(0);
		assertTrue(classifications.size() == 1);
		assertEquals("3.8", item1.itemKey);
		assertEquals("Küchen", item1.itemName);
		assertEquals("DIN277-2", item1.systemName);
		assertEquals("2005", item1.systemEdition);
	}
	@Test
	public void testSpace2BaseQuantities() {
		this.baseQuantitiesCurrentData = space2.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("NetFloorArea", "32.64");
		this.baseQuantitiesTestData.put("FinishCeilingHeight", "2.9");
		this.baseQuantitiesTestData.put("GrossPerimeter", "23.2");
		this.baseQuantitiesTestData.put("GrossFloorArea", "32.64");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = space2.getBaseQuantities();
		assertEquals(4, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("NetFloorArea")) {
				assertEquals("32.64", item.value);
				testCount++;
			} else if(item.name.equals("FinishCeilingHeight")) {
				assertEquals("2.9", item.value);
				testCount++;
			}else if(item.name.equals("GrossPerimeter")) {
				assertEquals("23.2", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossFloorArea")) {
				assertEquals("32.64", item.value);
				testCount++;
			}
		}
		assertEquals(4, testCount);*/
	}
	@Test
	public void testSpace2Properties() {
		List<PropertyList> spaceProperties = space2.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		assertEquals(3, propertyMap.size());
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("CeilingCovering", "IFCLABEL('Paint')");
		currentProperties.put("WallCovering", "IFCLABEL('Paint')");
		currentProperties.put("FloorCovering", "IFCLABEL('Tiles')");
		assertEquals(currentProperties, propertyMap);
		
		
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("CeilingCovering")) {
				assertEquals("IFCLABEL('Paint')", nvp.value);
				testCount++;
			} else if (nvp.name.equals("WallCovering")) {
				assertEquals("IFCLABEL('Paint')", nvp.value);
				testCount++;
			}else if (nvp.name.equals("FloorCovering")) {
				assertEquals("IFCLABEL('Tiles')", nvp.value);
				testCount++;
			}
		}
		assertEquals(3, testCount);*/
	}
	@Test
	public void testSpace2SpatialContainer() {
		SpaceSpatialContainer spatialContainer = space2.getSpatialContainer();
		assertEquals(2, spatialContainer.getCoverings().size());
		assertNull(spatialContainer.getDoorsWindows());
		assertEquals(3, spatialContainer.getElements().size());
	}
	@Test
	public void testSpace2Boundary() {
		List<BoundaryItem> boundary = space2.getSpaceBoundary();
		assertEquals(3, boundary.size());
		BoundaryItem item1 = boundary.get(0);
		BoundaryItem item2 = boundary.get(1);
		BoundaryItem item3 = boundary.get(2);
		BoundaryItem referenceItem1 = new BoundaryItem("2cs23eea03h3g387ZsghoF", "ifcdoor", "Physical", "Internal");
		BoundaryItem referenceItem2 = new BoundaryItem("2cs23eea036s33eLZsghoF", "ifcwindow", "Physical", "External");
		BoundaryItem referenceItem3 = new BoundaryItem("2cs23eea03hs33eLZsghoF", "ifcwindow", "Physical", "External");
		assertEquals(referenceItem1, item1);
		assertEquals(referenceItem2, item2);
		assertEquals(referenceItem3, item3);
		
	}
	@Test
	public void testSpace3() throws JAXBException {
		//super.outputXmlToSystemOut(space3);
	}
	@Test
	public void testSpace3Attributes(){
		Attributes attributes = space3.getAttributes();
		assertEquals("0h$ksovXH3Je2d9dH7s8af", attributes.getGuid());
		assertEquals("EG.003", attributes.getName());
		assertEquals("Space-003 object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("Hall", attributes.getLongName());
		assertEquals(EIfcinternalorexternalenum.toString(EIfcinternalorexternalenum.NOTDEFINED), attributes.getInternalExternal());
	}
	@Test
	public void testSpace3Classifications() {
		List<ClassificationItem> classifications = space3.getClassificationList();
		ClassificationItem item1 = classifications.get(0);
		assertTrue(classifications.size() == 1);
		assertEquals("7", item1.itemKey);
		assertEquals("sonstige Nutzflächen", item1.itemName);
		assertEquals("DIN277-2", item1.systemName);
		assertEquals("2005", item1.systemEdition);
	}
	@Test
	public void testSpace3BaseQuantities() {
		this.baseQuantitiesCurrentData = space3.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("NetFloorArea", "15.84");
		this.baseQuantitiesTestData.put("FinishCeilingHeight", "2.9");
		this.baseQuantitiesTestData.put("GrossPerimeter", "16.2");
		this.baseQuantitiesTestData.put("GrossFloorArea", "15.84");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = space3.getBaseQuantities();
		assertEquals(4, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("NetFloorArea")) {
				assertEquals("15.84", item.value);
				testCount++;
			} else if(item.name.equals("FinishCeilingHeight")) {
				assertEquals("2.9", item.value);
				testCount++;
			}else if(item.name.equals("GrossPerimeter")) {
				assertEquals("16.2", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossFloorArea")) {
				assertEquals("15.84", item.value);
				testCount++;
			}
		}
		assertEquals(4, testCount);*/
	}
	@Test
	public void testSpace3Properties() {
		List<PropertyList> spaceProperties = space3.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		assertEquals(3, propertyMap.size());
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("CeilingCovering", "IFCLABEL('Paint')");
		currentProperties.put("WallCovering", "IFCLABEL('Paint')");
		currentProperties.put("FloorCovering", "IFCLABEL('Tiles')");
		assertEquals(currentProperties, propertyMap);
		
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("CeilingCovering")) {
				assertEquals("IFCLABEL('Paint')", nvp.value);
				testCount++;
			} else if (nvp.name.equals("WallCovering")) {
				assertEquals("IFCLABEL('Paint')", nvp.value);
				testCount++;
			}else if (nvp.name.equals("FloorCovering")) {
				assertEquals("IFCLABEL('Tiles')", nvp.value);
				testCount++;
			}
		}
		assertEquals(3, testCount);*/
	}
	@Test
	public void testSpace3SpatialContainer() {
		SpaceSpatialContainer spatialContainer = space3.getSpatialContainer();
		assertNull(spatialContainer);
	
	}
	@Test
	public void testSpace3Boundary() {
		List<BoundaryItem> boundary = space3.getSpaceBoundary();
		assertEquals(null, boundary);
	}
	@Test
	public void testSpace4() throws JAXBException {
		//super.outputXmlToSystemOut(space4);
	}
	@Test
	public void testSpace4Attributes(){
		Attributes attributes = space4.getAttributes();
		assertEquals("0h$ksovXH3Jeg02dH7s8af", attributes.getGuid());
		assertEquals("EG.004", attributes.getName());
		assertEquals("Space-004 object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("Vestibule", attributes.getLongName());
		assertEquals(EIfcinternalorexternalenum.toString(EIfcinternalorexternalenum.NOTDEFINED), attributes.getInternalExternal());
	}
	@Test
	public void testSpace4Classifications() {
		List<ClassificationItem> classifications = space4.getClassificationList();
		ClassificationItem item1 = classifications.get(0);
		assertTrue(classifications.size() == 1);
		assertEquals("7", item1.itemKey);
		assertEquals("sonstige Nutzflächen", item1.itemName);
		assertEquals("DIN277-2", item1.systemName);
		assertEquals("2005", item1.systemEdition);
	}
	@Test
	public void testSpace4BaseQuantities() {
		this.baseQuantitiesCurrentData = space4.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("NetFloorArea", "15.84");
		this.baseQuantitiesTestData.put("FinishCeilingHeight", "2.9");
		this.baseQuantitiesTestData.put("GrossPerimeter", "16.2");
		this.baseQuantitiesTestData.put("GrossFloorArea", "15.84");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = space4.getBaseQuantities();
		assertEquals(4, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("NetFloorArea")) {
				assertEquals("15.84", item.value);
				testCount++;
			} else if(item.name.equals("FinishCeilingHeight")) {
				assertEquals("2.9", item.value);
				testCount++;
			}else if(item.name.equals("GrossPerimeter")) {
				assertEquals("16.2", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossFloorArea")) {
				assertEquals("15.84", item.value);
				testCount++;
			}
		}
		assertEquals(4, testCount);*/
	}
	@Test
	public void testSpace4Properties() {
		List<PropertyList> spaceProperties = space4.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		assertEquals(3, propertyMap.size());
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("CeilingCovering", "IFCLABEL('Paint')");
		currentProperties.put("WallCovering", "IFCLABEL('Paint')");
		currentProperties.put("FloorCovering", "IFCLABEL('Slate')");
		assertEquals(currentProperties, propertyMap);
		
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("CeilingCovering")) {
				assertEquals("IFCLABEL('Paint')", nvp.value);
				testCount++;
			} else if (nvp.name.equals("WallCovering")) {
				assertEquals("IFCLABEL('Paint')", nvp.value);
				testCount++;
			}else if (nvp.name.equals("FloorCovering")) {
				assertEquals("IFCLABEL('Slate')", nvp.value);
				testCount++;
			}
		}
		assertEquals(3, testCount);*/
	}
	@Test
	public void testSpace4SpatialContainer() {
		SpaceSpatialContainer spatialContainer = space4.getSpatialContainer();
		assertEquals(1, spatialContainer.getCoverings().size());
		assertEquals(2, spatialContainer.getDoorsWindows().size());
		assertEquals(3, spatialContainer.getElements().size());
	}
	@Test
	public void testSpace4Boundary() {
		List<BoundaryItem> boundary = space4.getSpaceBoundary();
		assertEquals(null, boundary);
	}
	@Test
	public void testSpace5() throws JAXBException {
		//super.outputXmlToSystemOut(space5);
	}
	@Test
	public void testSpace5Attributes(){
		Attributes attributes = space5.getAttributes();
		assertEquals("0h$ksovXH2xeg02dH7s8af", attributes.getGuid());
		assertEquals("OG1.001", attributes.getName());
		assertEquals("Space-005 object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("Terrace", attributes.getLongName());
		assertEquals(EIfcinternalorexternalenum.toString(EIfcinternalorexternalenum.NOTDEFINED), attributes.getInternalExternal());
	}
	@Test
	public void testSpace5Classifications() {
		List<ClassificationItem> classifications = space5.getClassificationList();
		ClassificationItem item1 = classifications.get(0);
		assertTrue(classifications.size() == 1);
		assertEquals("1", item1.itemKey);
		assertEquals("Wohnen und Aufenthalt", item1.itemName);
		assertEquals("DIN277-2", item1.systemName);
		assertEquals("2005", item1.systemEdition);
	}
	@Test
	public void testSpace5BaseQuantities() {
		this.baseQuantitiesCurrentData = space5.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("NetFloorArea", "71.4");
		this.baseQuantitiesTestData.put("GrossPerimeter", "34.4");
		this.baseQuantitiesTestData.put("GrossFloorArea", "71.4");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = space5.getBaseQuantities();
		assertEquals(3, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("NetFloorArea")) {
				assertEquals("71.4", item.value);
				testCount++;
			} else if(item.name.equals("FinishCeilingHeight")) {
				assertEquals("2.9", item.value);
				testCount++;
			}else if(item.name.equals("GrossPerimeter")) {
				assertEquals("34.4", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossFloorArea")) {
				assertEquals("71.4", item.value);
				testCount++;
			}
		}
		assertEquals(3, testCount);*/
	}
	@Test
	public void testSpace5Properties() {
		List<PropertyList> spaceProperties = space5.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		assertEquals(2, propertyMap.size());
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("Reference", "IFCLABEL('R-005')");
		currentProperties.put("FloorCovering", "IFCLABEL('Granite')");
		assertEquals(currentProperties, propertyMap);
		
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("Reference")) {
				assertEquals("IFCLABEL('R-005')", nvp.value);
				testCount++;
			}else if (nvp.name.equals("FloorCovering")) {
				assertEquals("IFCLABEL('Granite')", nvp.value);
				testCount++;
			}
		}
		assertEquals(testCount, 2);*/
	}
	@Test
	public void testSpace5SpatialContainer() {
		SpaceSpatialContainer spatialContainer = space5.getSpatialContainer();
		assertNull(spatialContainer);
	}
	@Test
	public void testSpace5Boundary() {
		List<BoundaryItem> boundary = space5.getSpaceBoundary();
		assertEquals(null, boundary);
	}
	@Test
	public void testSpace6() throws JAXBException {
		//super.outputXmlToSystemOut(space6);
	}
	@Test
	public void testSpace6Attributes(){
		Attributes attributes = space6.getAttributes();
		assertEquals("0h$kk5vXHc56g02dH7s8af", attributes.getGuid());
		assertEquals("OG1.002", attributes.getName());
		assertEquals("Space-006 object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("Meeting room", attributes.getLongName());
		assertEquals(EIfcinternalorexternalenum.toString(EIfcinternalorexternalenum.NOTDEFINED), attributes.getInternalExternal());
	}
	@Test
	public void testSpace6Classifications() {
		List<ClassificationItem> classifications = space6.getClassificationList();
		ClassificationItem item1 = classifications.get(0);
		assertTrue(classifications.size() == 1);
		assertEquals("1", item1.itemKey);
		assertEquals("Wohnen und Aufenthalt", item1.itemName);
		assertEquals("DIN277-2", item1.systemName);
		assertEquals("2005", item1.systemEdition);
	}
	@Test
	public void testSpace6BaseQuantities() {
		this.baseQuantitiesCurrentData = space6.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("GrossCeilingArea", "83.28");
		this.baseQuantitiesTestData.put("NetCeilingArea", "83.28");
		this.baseQuantitiesTestData.put("NetWallArea", "123.58");
		this.baseQuantitiesTestData.put("NetFloorArea", "83.28");
		this.baseQuantitiesTestData.put("FinishCeilingHeight", "2.9");
		this.baseQuantitiesTestData.put("GrossWallArea", "135.6");
		this.baseQuantitiesTestData.put("GrossPerimeter", "45.2");
		this.baseQuantitiesTestData.put("GrossFloorArea", "83.28");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = space6.getBaseQuantities();
		assertEquals(8, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("GrossCeilingArea")) {
				assertEquals("83.28", item.value);
				testCount++;
			} else if(item.name.equals("NetCeilingArea")) {
				assertEquals("83.28", item.value);
				testCount++;
			}else if(item.name.equals("NetWallArea")) {
				assertEquals("123.58", item.value);
				testCount++;
			}
			else if(item.name.equals("NetFloorArea")) {
				assertEquals("83.28", item.value);
				testCount++;
			}
			else if(item.name.equals("FinishCeilingHeight")) {
				assertEquals("2.9", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossWallArea")) {
				assertEquals("135.6", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossPerimeter")) {
				assertEquals("45.2", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossFloorArea")) {
				assertEquals("83.28", item.value);
				testCount++;
			}
		}
		
		assertEquals(8, testCount);*/
	}
	@Test
	public void testSpace6Properties() {
		List<PropertyList> spaceProperties = space6.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("Reference", "IFCLABEL('R-006')");
		currentProperties.put("FloorCovering", "IFCLABEL('Parquet')");
		currentProperties.put("CeilingCovering", "IFCLABEL('Paint')");
		currentProperties.put("WallCovering", "IFCLABEL('Paint')");
		assertEquals(currentProperties.size(), propertyMap.size());
		assertEquals(currentProperties, propertyMap);
		
		
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("Reference")) {
				assertEquals("IFCLABEL('R-006')", nvp.value);
				testCount++;
			}else if (nvp.name.equals("FloorCovering")) {
				assertEquals("IFCLABEL('Parquet')", nvp.value);
				testCount++;
			}
		}
		assertEquals(testCount, 2);*/
	}
	@Test
	public void testSpace6SpatialContainer() {
		SpaceSpatialContainer spatialContainer = space6.getSpatialContainer();
		assertNull(spatialContainer);
	}
	@Test
	public void testSpace6Boundary() {
		List<BoundaryItem> boundary = space6.getSpaceBoundary();
		assertEquals(2, boundary.size());
		BoundaryItem item1 = boundary.get(0);
		BoundaryItem item2 = boundary.get(1);
		BoundaryItem referenceItem1 = new BoundaryItem("2cs23eea03h3g3sDZsghoF", "ifcdoor", "Physical", "External");
		BoundaryItem referenceItem2 = new BoundaryItem("2cs23eejw3hs33eLZsghoF", "ifcwindow", "Physical", "External");
		assertEquals(referenceItem1, item1);
		assertEquals(referenceItem2, item2);
	}
	@Test
	public void testSpace7() throws JAXBException {
		//super.outputXmlToSystemOut(space7);
	}
	@Test
	public void testSpace7Attributes(){
		Attributes attributes = space7.getAttributes();
		assertEquals("0h$kk5vvG2xeg02dH7s8af", attributes.getGuid());
		assertEquals("OG1.003", attributes.getName());
		assertEquals("Space-007 object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("Kitchen", attributes.getLongName());
		assertEquals(EIfcinternalorexternalenum.toString(EIfcinternalorexternalenum.NOTDEFINED), attributes.getInternalExternal());
	}
	@Test
	public void testSpace7Classifications() {
		List<ClassificationItem> classifications = space7.getClassificationList();
		ClassificationItem item1 = classifications.get(0);
		assertTrue(classifications.size() == 1);
		assertEquals("1", item1.itemKey);
		assertEquals("Wohnen und Aufenthalt", item1.itemName);
		assertEquals("DIN277-2", item1.systemName);
		assertEquals("2005", item1.systemEdition);
	}
	@Test
	public void testSpace7BaseQuantities() {
		this.baseQuantitiesCurrentData = space7.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("GrossCeilingArea", "16.5");
		this.baseQuantitiesTestData.put("NetCeilingArea", "16.5");
		this.baseQuantitiesTestData.put("NetWallArea", "48.14");
		this.baseQuantitiesTestData.put("NetFloorArea", "16.5");
		this.baseQuantitiesTestData.put("FinishCeilingHeight", "2.9");
		this.baseQuantitiesTestData.put("GrossWallArea", "49.8");
		this.baseQuantitiesTestData.put("GrossPerimeter", "16.6");
		this.baseQuantitiesTestData.put("GrossFloorArea", "16.5");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = space7.getBaseQuantities();
		assertEquals(8, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("GrossCeilingArea")) {
				assertEquals("16.5", item.value);
				testCount++;
			} else if(item.name.equals("NetCeilingArea")) {
				assertEquals("16.5", item.value);
				testCount++;
			}else if(item.name.equals("NetWallArea")) {
				assertEquals("48.14", item.value);
				testCount++;
			}
			else if(item.name.equals("NetFloorArea")) {
				assertEquals("16.5", item.value);
				testCount++;
			}
			else if(item.name.equals("FinishCeilingHeight")) {
				assertEquals("2.9", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossWallArea")) {
				assertEquals("49.8", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossPerimeter")) {
				assertEquals("16.6", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossFloorArea")) {
				assertEquals("16.5", item.value);
				testCount++;
			}
		}
		
		assertEquals(8, testCount);*/
	}
	@Test
	public void testSpace7Properties() {
		List<PropertyList> spaceProperties = space7.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("Reference", "IFCLABEL('R-007')");
		currentProperties.put("FloorCovering", "IFCLABEL('Tiles')");
		currentProperties.put("CeilingCovering", "IFCLABEL('Paint')");
		currentProperties.put("WallCovering", "IFCLABEL('Paint')");
		assertEquals(currentProperties.size(), propertyMap.size());
		assertEquals(currentProperties, propertyMap);
		
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("Reference")) {
				assertEquals("IFCLABEL('R-007')", nvp.value);
				testCount++;
			}else if (nvp.name.equals("FloorCovering")) {
				assertEquals("IFCLABEL('Tiles')", nvp.value);
				testCount++;
			}
		}
		assertEquals(testCount, 2);*/
	}
	@Test
	public void testSpace7SpatialContainer() {
		SpaceSpatialContainer spatialContainer = space7.getSpatialContainer();
		assertNull(spatialContainer);
	}
	@Test
	public void testSpace7Boundary() {
		List<BoundaryItem> boundary = space7.getSpaceBoundary();
		assertEquals(null, boundary);
	}
	@Test
	public void testSpace8() throws JAXBException {
		//super.outputXmlToSystemOut(space8);
	}
	@Test
	public void testSpace8Attributes(){
		Attributes attributes = space8.getAttributes();
		assertEquals("0h$kk5vvG2xeg6vdH7s8af", attributes.getGuid());
		assertEquals("OG1.004", attributes.getName());
		assertEquals("Space-008 object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("Toilette", attributes.getLongName());
		assertEquals(EIfcinternalorexternalenum.toString(EIfcinternalorexternalenum.NOTDEFINED), attributes.getInternalExternal());
	}
	@Test
	public void testSpace8Classifications() {
		List<ClassificationItem> classifications = space8.getClassificationList();
		ClassificationItem item1 = classifications.get(0);
		assertTrue(classifications.size() == 1);
		assertEquals("7", item1.itemKey);
		assertEquals("sonstige Nutzflächen", item1.itemName);
		assertEquals("DIN277-2", item1.systemName);
		assertEquals("2005", item1.systemEdition);
	}
	@Test
	public void testSpace8BaseQuantities() {
		this.baseQuantitiesCurrentData = space8.getBaseQuantities().getElementMap();
		this.baseQuantitiesTestData = new HashMap<String, String>();
		this.baseQuantitiesTestData.put("GrossCeilingArea", "15.84");
		this.baseQuantitiesTestData.put("NetCeilingArea", "15.84");
		this.baseQuantitiesTestData.put("NetWallArea", "46.98");
		this.baseQuantitiesTestData.put("NetFloorArea", "15.84");
		this.baseQuantitiesTestData.put("FinishCeilingHeight", "2.9");
		this.baseQuantitiesTestData.put("GrossWallArea", "48.6");
		this.baseQuantitiesTestData.put("GrossPerimeter", "16.2");
		this.baseQuantitiesTestData.put("GrossFloorArea", "15.84");
		this.compareBaseQuantities();
		
		/*List<NameValuePair> basequantities = space8.getBaseQuantities();
		assertEquals(8, basequantities.size());
		int testCount = 0;
		for(NameValuePair item : basequantities) {
			if(item.name.equals("GrossCeilingArea")) {
				assertEquals("15.84", item.value);
				testCount++;
			} else if(item.name.equals("NetCeilingArea")) {
				assertEquals("15.84", item.value);
				testCount++;
			}else if(item.name.equals("NetWallArea")) {
				assertEquals("46.98", item.value);
				testCount++;
			}
			else if(item.name.equals("NetFloorArea")) {
				assertEquals("15.84", item.value);
				testCount++;
			}
			else if(item.name.equals("FinishCeilingHeight")) {
				assertEquals("2.9", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossWallArea")) {
				assertEquals("48.6", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossPerimeter")) {
				assertEquals("16.2", item.value);
				testCount++;
			}
			else if(item.name.equals("GrossFloorArea")) {
				assertEquals("15.84", item.value);
				testCount++;
			}
		}
		
		assertEquals(8, testCount);*/
	}
	@Test
	public void testSpace8Properties() {
		List<PropertyList> spaceProperties = space8.getProperties();
		assertEquals(1, spaceProperties.size());
		PropertyList pList = spaceProperties.get(0);
		assertEquals(CommonObjectImpl.Property.COMMON_PROPERTY.getKey(), pList.getName());
		Map<String, String> propertyMap = pList.getElementMap();
		
		Map<String, String> currentProperties = new HashMap<String, String>();
		currentProperties.put("Reference", "IFCLABEL('R-008')");
		currentProperties.put("FloorCovering", "IFCLABEL('Tiles')");
		currentProperties.put("CeilingCovering", "IFCLABEL('Paint')");
		currentProperties.put("WallCovering", "IFCLABEL('Tiles')");
		assertEquals(currentProperties.size(), propertyMap.size());
		assertEquals(currentProperties, propertyMap);
		
		/*int testCount = 0;
		for(NameValuePair nvp : pList.getProperties()) {
			if(nvp.name.equals("Reference")) {
				assertEquals("IFCLABEL('R-008')", nvp.value);
				testCount++;
			}else if (nvp.name.equals("FloorCovering")) {
				assertEquals("IFCLABEL('Tiles')", nvp.value);
				testCount++;
			}
		}
		assertEquals(testCount, 2);*/
	}
	@Test
	public void testSpace8SpatialContainer() {
		SpaceSpatialContainer spatialContainer = space8.getSpatialContainer();
		assertNull(spatialContainer);
	}
	@Test
	public void testSpace8Boundary() {
		List<BoundaryItem> boundary = space8.getSpaceBoundary();
		assertEquals(null, boundary);
	}
	@Test
	public void testSpatialDecompositionOfSpaces() {
		// storey 1
		this.testSpatialDecomposition(space1, "0h$ksovXH3Jeg0w$H7yFJf");
		this.testSpatialDecomposition(space2, "0h$ksovXH3Jeg0w$H7yFJf");
		this.testSpatialDecomposition(space3, "0h$ksovXH3Jeg0w$H7yFJf");
		this.testSpatialDecomposition(space4, "0h$ksovXH3Jeg0w$H7yFJf");
		//storey 2
		this.testSpatialDecomposition(space5, "0h$ksovXH3Jee5w$H7yFJf");
		this.testSpatialDecomposition(space6, "0h$ksovXH3Jee5w$H7yFJf");
		this.testSpatialDecomposition(space7, "0h$ksovXH3Jee5w$H7yFJf");
		this.testSpatialDecomposition(space8, "0h$ksovXH3Jee5w$H7yFJf");
	}
	
	public void testSpatialDecomposition(Space space, String guid) {
		//Map<String, String[]> spatialComposition = space.getSpatialDecomposition();
		Decomposition spatialComposition = space.getSpatialDecomposition();
		List<String> storeys = spatialComposition.getBuildingStoreys();
		assertEquals(storeys.size(), 1);
		assertEquals(guid, storeys.get(0) );
	}
}
