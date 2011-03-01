package no.bimconverter.ifc.v2x3.object.element;


import static junit.framework.Assert.assertNotNull;
import static org.junit.Assert.assertEquals;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;



import no.bimconverter.ifc.IfcTestMethods;
import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.jaxb.Attributes;
import no.bimconverter.ifc.jaxb.BoundaryItem;
import no.bimconverter.ifc.jaxb.Decomposition;
import no.bimconverter.ifc.jaxb.NameValuePair;
import no.bimconverter.ifc.jaxb.PropertyList;
import no.bimconverter.ifc.jaxb.SpatialContainerItem;
import no.bimconverter.ifc.v2x3.IfcModelImpl;
import no.bimconverter.ifc.v2x3.object.element.type.DoorStyle;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class DoorTest extends IfcTestMethods{

	
	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	
	
	
	
	private List<Door> doors;
	private Door door1;
	private Door door2;
	private Door door3;
	private Door door4;
	private Door[] doorArray;
	private DoorStyle doorStyle1;
	private DoorStyle doorStyle2;
	private DoorStyle doorStyle3;
	private DoorStyle doorStyle4;
	
	Attributes attributes = null;
	String[] attributesArray = null;
	Attributes testAttributes = null;
	List<PropertyList> windowStyleproperties = null;
	
	List<NameValuePair> nvpListBeingTested = null;
	Map<String, String> propertyMapBeingTested = null;
	String propertySetShouldBeOfType = null;
	String propertySetShouldHaveName = null;
	
	private Map<String, String> mapBasedTestData;
	private PropertyList currentPropertyList;
	private List<BoundaryItem> boundary;
	private BoundaryItem referenceItem;
	
	
	@Before
	public void setUp() {
		super.createTestRepo();
		doors = model.getDoors();
		door1 = doors.get(0);
		door2 = doors.get(1);
		door3 = doors.get(2);
		door4 = doors.get(3);
		doorStyle1 = door1.getDoorStyle();
		doorStyle2 = door2.getDoorStyle();
		doorStyle3 = door3.getDoorStyle();
		doorStyle4 = door4.getDoorStyle();
		doorArray = new Door[]{door1,door2,door3,door4};
	}
	@After
	public void tearDown() {
		
	}
	
	@Test
	public void testWindowObject() throws JAXBException {
		assertEquals(4, doors.size()); 
		assertNotNull(doors);
	}
	
	public void testDisplayDoor(Door theDoor) throws JAXBException {
		assertNotNull(theDoor);
		//super.outputXmlToSystemOut(theDoor);
	}
	@Test
	public void testDisplayDoor1() throws JAXBException {
		testDisplayDoor(door1);
	}
	@Test
	public void testDisplayDoor2() throws JAXBException {
		testDisplayDoor(door2);
	}
	@Test
	public void testDisplayDoor3() throws JAXBException {
		testDisplayDoor(door3);
	}
	@Test
	public void testDisplayDoor4() throws JAXBException {
		testDisplayDoor(door4);
	}
	@Test
	public void testAttributes() {
		attributes = door1.getAttributes();
		attributesArray = new String[]{"2cs23eea03hgs3eLZsghoF",
										"AT-1.EG.004",
										"Door-001 for testing Basic FM Handover"};
		testAttributes = new Attributes();
		testAttributes.setGuid("2cs23eea03hgs3eLZsghoF");
		testAttributes.setName("AT-1.EG.004");
		testAttributes.setDescription("Door-001 for testing Basic FM Handover");
		
		
	
		this.testDoorAttributes();
		
		attributes = door2.getAttributes();
		attributesArray = new String[]{"2cs23eea03h3g3eLZsghoF",
										"IT-1.EG.004",
										null};
		testAttributes = new Attributes();
		testAttributes.setGuid("2cs23eea03h3g3eLZsghoF");
		testAttributes.setName("IT-1.EG.004");
		//testAttributes.setDescription("Door-001 for testing Basic FM Handover");
		
		this.testDoorAttributes();
		
		attributes = door3.getAttributes();
		attributesArray = new String[]{"2cs23eea03h3g387ZsghoF",
										"IT-2.EG.002",
										null};
		testAttributes = new Attributes();
		testAttributes.setGuid("2cs23eea03h3g387ZsghoF");
		testAttributes.setName("IT-2.EG.002");
		//testAttributes.setDescription("Door-001 for testing Basic FM Handover");
		this.testDoorAttributes();
		
		attributes = door4.getAttributes();
		attributesArray = new String[]{"2cs23eea03h3g3sDZsghoF",
										"IT-2.EG.002",
										null};
		testAttributes = new Attributes();
		testAttributes.setGuid("2cs23eea03h3g3sDZsghoF");
		testAttributes.setName("IT-2.EG.002");
		this.testDoorAttributes();
	}
	public void testDoorAttributes() {
		assertEquals(attributes, testAttributes);
	}
	
	@Test
	public void testDoorStyleAttributes() {
		
		attributes = doorStyle1.getAttributes();
//		attributesArray = new String[]{"3Yq2sq7brEif5ojF_S9$NM",
//				"single right swing door - internal",
//				"unset",
//				null,
//				"DOUBLE_PANEL_VERTICAL"};
		testAttributes = new Attributes();
		testAttributes.setGuid("3Yq2sq7brEif5ojF_S9$NM");
		testAttributes.setName("single right swing door - internal");
		testAttributes.setConstructionType("NOTDEFINED");
		testAttributes.setDescription(null);
		testAttributes.setOperationType("SINGLE_SWING_RIGHT");
		//testAttributes.setDescription("Door-001 for testing Basic FM Handover");
		
		this.testWindowStyleAttributes();
		
		attributes = doorStyle2.getAttributes();
//		attributesArray = new String[]{"3Yq2sq7brEif5ojF_S4gNM",
//				"single left swing door - internal",
//				"unset",
//				null,
//				"SINGLE_PANEL"};
		testAttributes = new Attributes();
		testAttributes.setGuid("3Yq2sq7brEif5ojF_S4gNM");
		testAttributes.setName("single left swing door - internal");
		testAttributes.setConstructionType("NOTDEFINED");
		testAttributes.setDescription(null);
		testAttributes.setOperationType("SINGLE_SWING_LEFT");
		
		this.testWindowStyleAttributes();
		
		attributes = doorStyle3.getAttributes();
//		attributesArray = new String[]{"3Yq2sq7bdWif5ojF_S4gNM",
//				"double swing door - internal",
//				"unset",
//				null,
//				"TRIPLE_PANEL_RIGHT"};
		testAttributes = new Attributes();
		testAttributes.setGuid("3Yq2sq7bdWif5ojF_S4gNM");
		testAttributes.setName("double swing door - internal");
		testAttributes.setConstructionType("NOTDEFINED");
		testAttributes.setDescription(null);
		testAttributes.setOperationType("DOUBLE_DOOR_DOUBLE_SWING");
		this.testWindowStyleAttributes();
		
		attributes = doorStyle4.getAttributes();
//		attributesArray = new String[]{"3Yq2sq7bdwif5ojF_S4gNM",
//				"double swing door - external",
//				"unset",
//				null,
//				"TRIPLE_PANEL_RIGHT"};
		testAttributes = new Attributes();
		testAttributes.setGuid("3Yq2sq7bdwif5ojF_S4gNM");
		testAttributes.setName("double swing door - external");
		testAttributes.setConstructionType("NOTDEFINED");
		testAttributes.setDescription(null);
		testAttributes.setOperationType("DOUBLE_DOOR_DOUBLE_SWING");
		
		this.testWindowStyleAttributes();

	}
	public void testWindowStyleAttributes() {
		assertEquals(attributes, testAttributes);
//		assertEquals(attributesArray[0], attributes.get(CommonElement.ATTRIBUTE_KEY_GUID));
//		assertEquals(attributesArray[1], attributes.get(CommonElement.ATTRIBUTE_KEY_NAME));
//		assertEquals(attributesArray[2], attributes.get(WindowStyle.Attribute.CONSTRUCTION_TYPE.getKey()));
//		assertEquals(attributesArray[3], attributes.get(CommonElement.ATTRIBUTE_KEY_DESCRIPTION));
//		assertEquals(attributesArray[4], attributes.get(WindowStyle.Attribute.OPERATION_TYPE.getKey()));
	}
	
	public void testPropertiesSet() {
		assertEquals(propertySetShouldBeOfType, currentPropertyList.getType());
		assertEquals(propertySetShouldHaveName, currentPropertyList.getName());
		
		//this.testNVPlist();
		this.testMap();
	}
	private void testMap() {
		assertEquals(propertyMapBeingTested.size(), mapBasedTestData.size());
		assertEquals(propertyMapBeingTested, mapBasedTestData);
	}
	public void testNVPlist() {
		assertEquals(nvpListBeingTested.size(), mapBasedTestData.size());
		for(NameValuePair nvp : nvpListBeingTested) {
			assertEquals(nvp.value, mapBasedTestData.get(nvp.name));
		}
	}
	@Test
	public void testDoor1PropertiesSet() {
		currentPropertyList = door1.getProperties().get(0);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		
		propertySetShouldBeOfType = null; //"Pset_DoorCommon";
		propertySetShouldHaveName = "Pset_DoorCommon";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("IsExternal", "IFCLOGICAL(.T.)");
		mapBasedTestData.put("FireRating", "IFCLABEL('T90')");
		mapBasedTestData.put("FireResistence", "IFCLABEL('T60')");
		mapBasedTestData.put("Reference", "IFCLABEL('AT-1')");
		this.testPropertiesSet();
	}
	@Test
	public void testDoor2PropertiesSet() {
		currentPropertyList = door2.getProperties().get(0);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = null; //"Pset_DoorCommon";
		propertySetShouldHaveName = "Pset_DoorCommon";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("IsExternal", "IFCLOGICAL(.F.)");
		mapBasedTestData.put("GlazingAreaFraction", "IFCPOSITIVERATIOMEASURE(0.6)");
		mapBasedTestData.put("FireResistence", "IFCLABEL('T30')");
		mapBasedTestData.put("Reference", "IFCLABEL('IT-1')");
		this.testPropertiesSet();
	}
	@Test
	public void testDoor3PropertiesSet() {
		currentPropertyList = door3.getProperties().get(0);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = null; //"Pset_DoorCommon";
		propertySetShouldHaveName = "Pset_DoorCommon";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("IsExternal", "IFCLOGICAL(.F.)");
		mapBasedTestData.put("GlazingAreaFraction", "IFCPOSITIVERATIOMEASURE(0.2)");
		mapBasedTestData.put("FireResistence", "IFCLABEL('T30')");
		mapBasedTestData.put("Reference", "IFCLABEL('IT-2')");
		this.testPropertiesSet();
	}
	@Test
	public void testDoor4PropertiesSet() {
		currentPropertyList = door4.getProperties().get(0);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = null; //"Pset_DoorCommon";
		propertySetShouldHaveName = "Pset_DoorCommon";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("IsExternal", "IFCLOGICAL(.T.)");
		mapBasedTestData.put("GlazingAreaFraction", "IFCPOSITIVERATIOMEASURE(0.8)");
		mapBasedTestData.put("FireResistence", "IFCLABEL('T30')");
		mapBasedTestData.put("Reference", "IFCLABEL('AT-2')");
		this.testPropertiesSet();
	}
	@Test
	public void testDoorStylePropertiesSet() {
		currentPropertyList = doorStyle1.getProperties().get(0);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = "ifcdoorliningproperties";
		propertySetShouldHaveName = null;
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Liningdepth", "0.2");
		mapBasedTestData.put("Liningthickness", "0.05");		
		this.testPropertiesSet();
		// test for door 2 property set 1
		currentPropertyList = doorStyle2.getProperties().get(0);
		
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		this.testPropertiesSet();
		// test for door 3 property set 1
		currentPropertyList = doorStyle3.getProperties().get(0);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		this.testPropertiesSet();
		// test for door 4 property set 1
		currentPropertyList = doorStyle4.getProperties().get(0);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		this.testPropertiesSet();
		// test for door 1 property set 2
		currentPropertyList = doorStyle1.getProperties().get(1);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = "ifcdoorpanelproperties"; 
		propertySetShouldHaveName = null;
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Panelposition", "2");
		mapBasedTestData.put("Paneldepth", "0.05");
		mapBasedTestData.put("Paneloperation", "1");
		mapBasedTestData.put("Panelwidth", "1.0");		
		this.testPropertiesSet();
		// test for door 2 property set 2
		currentPropertyList = doorStyle2.getProperties().get(1);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		// test for door 3 property set 2
		currentPropertyList = doorStyle3.getProperties().get(1);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		// test for door 4 property set 2
		currentPropertyList = doorStyle4.getProperties().get(1);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		
		// test for door 3 property set 3
		currentPropertyList = doorStyle3.getProperties().get(2);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = "ifcdoorpanelproperties"; 
		propertySetShouldHaveName = null;
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Panelposition", "3");
		mapBasedTestData.put("Paneldepth", "0.05");
		mapBasedTestData.put("Paneloperation", "1");
		mapBasedTestData.put("Panelwidth", "0.5");
		// test for door 4 property set 3
		currentPropertyList = doorStyle4.getProperties().get(2);
		//nvpListBeingTested = currentPropertyList.getProperties();
		propertyMapBeingTested = currentPropertyList.getElementMap();
		
		
	}
	
	@Test
	public void testDoorMaterials() {
		for(Door d : doorArray) {
			assertEquals(null, d.getMaterial());
		}
	}
	@Test
	public void testDoorBoundaries() {
		boundary = door1.getSpaceBoundary();
		assertEquals(null, boundary);
		boundary = door2.getSpaceBoundary();
		assertEquals(null, boundary);
		boundary = door3.getSpaceBoundary();
		referenceItem = new BoundaryItem("0h$ksovXH3Jeg0w$H7s8af", "ifcspace", "Physical", "Internal");
		assertEquals(referenceItem, boundary.get(0));
		referenceItem = new BoundaryItem("0h$ksovXH3Jeg0w$H724af", "ifcspace", "Physical", "Internal");
		assertEquals(referenceItem, boundary.get(1));
		boundary = door4.getSpaceBoundary();
		referenceItem = new BoundaryItem("0h$kk5vXHc56g02dH7s8af", "ifcspace", "Physical", "External");
		assertEquals(referenceItem, boundary.get(0));
	}
	
	@Test
	public void testSpatialContainers() {
		List<SpatialContainerItem> spatialContainer = door1.getSpatialcontainer();
		Decomposition spatialDecompostion = door1.getSpatialDecomposition();
		assertNotNull(spatialDecompostion.getSpaces());
		assertEquals("0h$ksovXH3Jeg02dH7s8af", spatialDecompostion.getSpaces().get(0));
		
//		spatialContainer = door2.getSpatialcontainer();
//		assertEquals(1, spatialContainer.size());
//		assertEquals("0h$ksovXH3Jeg02dH7s8af", spatialContainer.get(0).guid);
//		assertEquals("ifcspace", spatialContainer.get(0).type);
		spatialDecompostion = door2.getSpatialDecomposition();
		assertNotNull(spatialDecompostion.getSpaces());
		assertEquals("0h$ksovXH3Jeg02dH7s8af", spatialDecompostion.getSpaces().get(0));
		
		/*spatialContainer = door3.getSpatialcontainer();
		assertEquals(1, spatialContainer.size());
		assertEquals("0h$ksovXH3Jeg0w$H7yFJf", spatialContainer.get(0).guid);
		assertEquals("ifcbuildingstorey", spatialContainer.get(0).type);*/
		
		spatialDecompostion = door3.getSpatialDecomposition();
		assertNotNull(spatialDecompostion.getBuildingStoreys());
		assertEquals("0h$ksovXH3Jeg0w$H7yFJf", spatialDecompostion.getBuildingStoreys().get(0));
		
		/*spatialContainer = door4.getSpatialcontainer();
		assertEquals(1, spatialContainer.size());
		assertEquals("0h$ksovXH3Jee5w$H7yFJf", spatialContainer.get(0).guid);
		assertEquals("ifcbuildingstorey", spatialContainer.get(0).type);*/
		spatialDecompostion = door4.getSpatialDecomposition();
		assertNotNull(spatialDecompostion.getBuildingStoreys());
		assertEquals("0h$ksovXH3Jee5w$H7yFJf", spatialDecompostion.getBuildingStoreys().get(0));
		
	}
	
	@Test
	public void testBaseQuantities() {
		
		assertEquals(null, door1.getBaseQuantities());
		assertEquals(null, door2.getBaseQuantities());
		propertyMapBeingTested = door3.getBaseQuantities().getElementMap();
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Area", "2.25");
		mapBasedTestData.put("Height", "2.25");
		mapBasedTestData.put("Width", "1.0");
		this.testMap();
		
		assertEquals(null, door4.getBaseQuantities());
	}
	
	
	
}
