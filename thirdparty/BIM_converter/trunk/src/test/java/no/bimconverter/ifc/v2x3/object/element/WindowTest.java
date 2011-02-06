package no.bimconverter.ifc.v2x3.object.element;


import static junit.framework.Assert.assertNotNull;
import static org.junit.Assert.assertEquals;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;



import no.bimconverter.ifc.Repositories;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.jaxb.Attributes;
import no.bimconverter.ifc.jaxb.BoundaryItem;
import no.bimconverter.ifc.jaxb.Decomposition;
import no.bimconverter.ifc.jaxb.NameValuePair;
import no.bimconverter.ifc.jaxb.PropertyList;
import no.bimconverter.ifc.v2x3.IfcModelImpl;
import no.bimconverter.ifc.v2x3.object.element.type.WindowStyle;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class WindowTest {

	String testingRepository = "FMHandoverRepository";
	String nonExistingRepository = "dummmmmyRepoThatDoesNotExist";
	//String testIfcFileName = "sample.ifc";
	String testIfcFileName = "20091007_Test_BasicFM-HandOver_01_valid.ifc";
	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	String ifcFilename = null;
	private IfcModelImpl model;
	
	Repositories repo = null;
	private List<Window> windows;
	private Window window1;
	private Window window2;
	private Window window3;
	private WindowStyle windowStyle1;
	private WindowStyle windowStyle2;
	private WindowStyle windowStyle3;
	Attributes attributes = null;
	Attributes testAttributes = null;
	String[] attributesArray = null;
	List<PropertyList> windowStyleproperties = null;
	
	List<NameValuePair> nvpListBeingTested = null;
	Map<String, String> propertyMapBeingTested = null;
	String propertySetShouldBeOfType = null;
	
	private Map<String, String> mapBasedTestData;
	private PropertyList currentPropertyList;
	private List<BoundaryItem> boundary;
	private BoundaryItem referenceItemn;
	
	
	@Before
	public void setUp() {
		model = new IfcModelImpl(testingRepository);
		ifcFilename = getClass().getResource( "/" +testIfcFileName ).toString();
		repo = new RepositoriesImpl();
		repo.addRepository(testingRepository, ifcFilename);
		windows = model.getWindows();
		window1 = windows.get(0);
		window2 = windows.get(1);
		window3 = windows.get(2);
		windowStyle1 = window1.getWindowStyle();
		windowStyle2 = window2.getWindowStyle();
		windowStyle3 = window3.getWindowStyle();
	}
	@After
	public void tearDown() {
		repo.deleteRepository(testingRepository);
	}
	
	@Test
	public void testWindowObject() throws JAXBException {
		assertEquals(3, windows.size()); 
		assertNotNull(windows);
	}
	
	@Test
	public void testDisplayWindow1() throws JAXBException {
		assertNotNull(window1);
		JAXBContext jc = JAXBContext.newInstance(Window.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal(window1, System.out );
	}
	@Test
	public void testDisplayWindow2() throws JAXBException {
		assertNotNull(window1);
		JAXBContext jc = JAXBContext.newInstance(Window.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal(window2, System.out );
	}
	@Test
	public void testDisplayWindow3() throws JAXBException {
		assertNotNull(window1);
		JAXBContext jc = JAXBContext.newInstance(Window.class);
		Marshaller m = jc.createMarshaller();
		m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal(window3, System.out );
	}
	@Test
	public void testWindow1Attributes() {
		attributes = window1.getAttributes();
		attributesArray = new String[]{"2cs23eea03hs33eLZsghoF",
										"AF-2.EG.002",
										"Window-002 for testing Basic FM Handover"};
		this.testAttributes = new Attributes();
		this.testAttributes.setGuid(attributesArray[0]);
		this.testAttributes.setName(attributesArray[1]);
		this.testAttributes.setDescription(attributesArray[2]);
		this.testWindowAttributes();
	}
	@Test
	public void testWindow2Attributes() {
		attributes = window2.getAttributes();
		attributesArray = new String[]{"2cs23eea036s33eLZsghoF",
										"AF-2.EG.002",
										"Window-002 for testing Basic FM Handover"};
		this.testAttributes = new Attributes();
		this.testAttributes.setGuid(attributesArray[0]);
		this.testAttributes.setName(attributesArray[1]);
		this.testAttributes.setDescription(attributesArray[2]);
		this.testWindowAttributes();
		
	}
	@Test
	public void testWindow3Attributes() {
		attributes = window3.getAttributes();
		attributesArray = new String[]{"2cs23eejw3hs33eLZsghoF",
										"AF-1.OG1.002",
										null};
		this.testAttributes = new Attributes();
		this.testAttributes.setGuid(attributesArray[0]);
		this.testAttributes.setName(attributesArray[1]);
		this.testAttributes.setDescription(attributesArray[2]);
		this.testWindowAttributes();
	}
	public void testWindowAttributes() {
		assertEquals(attributes, testAttributes);
//		assertEquals(attributesArray[0], attributes.get(CommonElement.ATTRIBUTE_KEY_GUID));
//		assertEquals(attributesArray[1], attributes.get(CommonElement.ATTRIBUTE_KEY_NAME));
//		assertEquals(attributesArray[2], attributes.get(CommonElement.ATTRIBUTE_KEY_DESCRIPTION));
	}
	public void testWindowStyleAttributes() {
		assertEquals(attributes, testAttributes);
//		assertEquals(attributesArray[0], attributes.get(CommonElement.ATTRIBUTE_KEY_GUID));
//		assertEquals(attributesArray[1], attributes.get(CommonElement.ATTRIBUTE_KEY_NAME));
//		assertEquals(attributesArray[2], attributes.get(WindowStyle.Attribute.CONSTRUCTION_TYPE.getKey()));
//		assertEquals(attributesArray[3], attributes.get(CommonElement.ATTRIBUTE_KEY_DESCRIPTION));
//		assertEquals(attributesArray[4], attributes.get(WindowStyle.Attribute.OPERATION_TYPE.getKey()));
	}
	@Test
	public void testWindow1WindowStyleAttributes() {
		
		attributes = windowStyle1.getAttributes();
		attributesArray = new String[]{"3Yq2sq7brEif5ojF_gf$NM",
				"tilt and turn two panel window - external",
				"NOTDEFINED",
				null,
				"DOUBLE_PANEL_HORIZONTAL"};
		testAttributes = new Attributes();
		testAttributes.setGuid(attributesArray[0]);
		testAttributes.setName(attributesArray[1]);
		testAttributes.setConstructionType(attributesArray[2]);
		testAttributes.setDescription(attributesArray[3]);
		testAttributes.setOperationType(attributesArray[4]);
		this.testWindowStyleAttributes();

	}
	@Test
	public void testWindow2WindowStyleAttributes() {
		
		attributes = windowStyle2.getAttributes();
		attributesArray = new String[]{"3Yq2sq7brEif5ojF_gf$NM",
				"tilt and turn two panel window - external",
				"NOTDEFINED",
				null,
				"DOUBLE_PANEL_HORIZONTAL"};
		testAttributes = new Attributes();
		testAttributes.setGuid(attributesArray[0]);
		testAttributes.setName(attributesArray[1]);
		testAttributes.setConstructionType(attributesArray[2]);
		testAttributes.setDescription(attributesArray[3]);
		testAttributes.setOperationType(attributesArray[4]);
		this.testWindowStyleAttributes();
		
	}
	@Test
	public void testWindow3WindowStyleAttributes() {
		
		attributes = windowStyle3.getAttributes();
		attributesArray = new String[]{"3Yq2sj3brEif5ojF_gf$NM",
				"fixed glazing window",
				"NOTDEFINED",
				null,
				"SINGLE_PANEL"};
		testAttributes = new Attributes();
		testAttributes.setGuid(attributesArray[0]);
		testAttributes.setName(attributesArray[1]);
		testAttributes.setConstructionType(attributesArray[2]);
		testAttributes.setDescription(attributesArray[3]);
		testAttributes.setOperationType(attributesArray[4]);
		this.testWindowStyleAttributes();
		
	}
	@Test
	public void testWindow1WindowStyleMaterial() {
		assertEquals(null, windowStyle1.getMaterial());
	}
	@Test
	public void testWindow2WindowStyleMaterial() {
		assertEquals(null, windowStyle2.getMaterial());
	}
	@Test
	public void testWindow3WindowStyleMaterial() {
		assertEquals(null, windowStyle3.getMaterial());
	}
	@Test
	public void testWindow1WindowStyleProperties() {
		this.windowStyleproperties = windowStyle1.getProperties();
		this.testWindowStyle1PropertiesSet1();
		this.testWindowStyle1PropertiesSet2();
		this.testWindowStyle1PropertiesSet3();
	}
	
	@Test
	public void testWindow2WindowStyleProperties() {
		this.windowStyleproperties = windowStyle2.getProperties();
		this.testWindowStyle1PropertiesSet1();
		this.testWindowStyle1PropertiesSet2();
		this.testWindowStyle1PropertiesSet3();
	}
	@Test
	public void testWindow3WindowStyleProperties() {
		this.windowStyleproperties = windowStyle3.getProperties();
		this.testWindowStyle3PropertiesSet1();
		this.testWindowStyle3PropertiesSet2();
	}
	
	public void testWindowStylePropertiesSet() {
		assertEquals(propertySetShouldBeOfType, currentPropertyList.getType());
		
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
	
	public void testWindowStyle1PropertiesSet1() {
		currentPropertyList = windowStyleproperties.get(0);
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = "ifcwindowliningproperties";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Liningdepth", "0.2");
		mapBasedTestData.put("Liningthickness", "0.05");
		mapBasedTestData.put("Transomthickness", "0.05");
		mapBasedTestData.put("Firsttransomoffset", "0.5");
		this.testWindowStylePropertiesSet();
	}
	public void testWindowStyle1PropertiesSet2() {
		currentPropertyList = windowStyleproperties.get(1);
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = "ifcwindowpanelproperties";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Operationtype", "SIDEHUNGLEFTHAND");
		mapBasedTestData.put("Panelposition", "LEFT");
		mapBasedTestData.put("Framedepth", "0.05");
		mapBasedTestData.put("Framethickness", "0.05");
		this.testWindowStylePropertiesSet();
	}
	public void testWindowStyle1PropertiesSet3() {
		currentPropertyList = windowStyleproperties.get(2);
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = "ifcwindowpanelproperties";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Operationtype", "TILTANDTURNRIGHTHAND");
		mapBasedTestData.put("Panelposition", "RIGHT");
		mapBasedTestData.put("Framedepth", "0.05");
		mapBasedTestData.put("Framethickness", "0.05");
		this.testWindowStylePropertiesSet();
	}
	
	public void testWindowStyle3PropertiesSet1() {
		currentPropertyList = windowStyleproperties.get(0);
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = "ifcwindowliningproperties";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Liningdepth", "0.2");
		mapBasedTestData.put("Liningthickness", "0.05");
		this.testWindowStylePropertiesSet();
	}
	public void testWindowStyle3PropertiesSet2() {
		currentPropertyList = windowStyleproperties.get(1);
		propertyMapBeingTested = currentPropertyList.getElementMap();
		propertySetShouldBeOfType = "ifcwindowpanelproperties";
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Operationtype", "REMOVABLECASEMENT");
		mapBasedTestData.put("Panelposition", "MIDDLE");
		mapBasedTestData.put("Framedepth", "0.05");
		mapBasedTestData.put("Framethickness", "0.05");
		this.testWindowStylePropertiesSet();
	}
	@Test
	public void testWindow1Classification() {
		assertEquals(null, window1.getClassificationList());
	}
	@Test
	public void testWindow2Classification() {
		assertEquals(null, window2.getClassificationList());
	}
	@Test
	public void testWindow3Classification() {
		assertEquals(null, window3.getClassificationList());
	}
	
	@Test
	public void testWindow1BaseQuantities() {
		//nvpListBeingTested = window1.getBaseQuantities();
		propertyMapBeingTested = window1.getBaseQuantities().getElementMap();
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Area", "3.0");
		mapBasedTestData.put("Height", "2.0");
		mapBasedTestData.put("Width", "1.5");
		this.testMap();
	}
	
	@Test
	public void testWindow2BaseQuantities() {
		propertyMapBeingTested = window2.getBaseQuantities().getElementMap();
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Area", "3.0");
		mapBasedTestData.put("Height", "2.0");
		mapBasedTestData.put("Width", "1.5");
		this.testMap();
	}
	@Test
	public void testWindow3BaseQuantities() {
		propertyMapBeingTested = window3.getBaseQuantities().getElementMap();
		mapBasedTestData = new HashMap<String, String>();
		mapBasedTestData.put("Area", "3.0");
		mapBasedTestData.put("Height", "1.0");
		mapBasedTestData.put("Width", "3.0");
		this.testMap();
	}
	
	
	@Test
	public void testSpatialContainer1() {
		Decomposition spatialDecompostion = window1.getSpatialDecomposition();
		assertEquals(1, spatialDecompostion.getBuildingStoreys().size());
		assertEquals("0h$ksovXH3Jeg0w$H7yFJf", spatialDecompostion.getBuildingStoreys().get(0));
	}
	@Test
	public void testSpatialContainer2() {
		
		
		Decomposition spatialDecompostion = window2.getSpatialDecomposition();
		assertEquals(1, spatialDecompostion.getBuildingStoreys().size());
		assertEquals("0h$ksovXH3Jeg0w$H7yFJf", spatialDecompostion.getBuildingStoreys().get(0));
	}
	@Test
	public void testSpatialContainer3() {
		
		
		Decomposition spatialDecompostion = window3.getSpatialDecomposition();
		assertEquals(1, spatialDecompostion.getBuildingStoreys().size());
		assertEquals("0h$ksovXH3Jee5w$H7yFJf", spatialDecompostion.getBuildingStoreys().get(0));
	}
	
	@Test
	public void testWindow1Boundary() {
		boundary = window1.getSpaceBoundary();
		referenceItemn = new BoundaryItem("0h$ksovXH3Jeg0w$H7s8af", "ifcspace", "Physical", "External");
		this.testWindowBoundary();
	}
	@Test
	public void testWindow2Boundary() {
		boundary = window2.getSpaceBoundary();
		referenceItemn = new BoundaryItem("0h$ksovXH3Jeg0w$H7s8af", "ifcspace", "Physical", "External");
		this.testWindowBoundary();
	}
	@Test
	public void testWindow3Boundary() {
		boundary = window3.getSpaceBoundary();
		referenceItemn = new BoundaryItem("0h$kk5vXHc56g02dH7s8af", "ifcspace", "Physical", "External");
		this.testWindowBoundary();
	}
	

	public void testWindowBoundary() {
		assertEquals(1, boundary.size());
		BoundaryItem item1 = boundary.get(0);
		assertEquals(referenceItemn, item1);
	}
	
	
}
