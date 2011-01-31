package no.bimconverter.ifc.jaxb;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import javax.xml.bind.JAXBElement;
import org.junit.*;
import static org.junit.Assert.*;

/**
 * The class <code>ElementListTest</code> contains tests for the class <code>{@link ElementList}</code>.
 *
 * @generatedBy CodePro at 27.01.11 10:06
 * @author PTHORSTE
 * @version $Revision: 1.0 $
 */
public class ElementListTest {
	/**
	 * Run the ElementList() constructor test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@Test
	public void testElementList_1()
		throws Exception {

		ElementList result = new ElementList();

		// add additional test code here
		assertNotNull(result);
		assertEquals(null, result.getElementMap());
	}

	/**
	 * Run the void addElement(String,String) method test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@Test
	public void testAddElement_1()
		throws Exception {
		ElementList fixture = new ElementList();
		fixture.elementList = new ArrayList<JAXBElement<String>>();
		String name = "";
		String value = "";

		fixture.addElement(name, value);

		// add additional test code here
	}

	/**
	 * Run the boolean changeElementValue(String,String) method test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@Test
	public void testChangeElementValue_1()
		throws Exception {
		ElementList fixture = new ElementList();
		fixture.elementList = new ArrayList<JAXBElement<String>>();
		String name = "";
		String value = "";

		boolean result = fixture.changeElementValue(name, value);

		// add additional test code here
		assertEquals(false, result);
	}

	/**
	 * Run the boolean changeElementValue(String,String) method test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@Test
	public void testChangeElementValue_2()
		throws Exception {
		ElementList fixture = new ElementList();
		fixture.elementList = new ArrayList<JAXBElement<String>>();
		String name = "";
		String value = "";

		boolean result = fixture.changeElementValue(name, value);

		// add additional test code here
		assertEquals(false, result);
	}

	/**
	 * Run the boolean changeElementValue(String,String) method test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@Test
	public void testChangeElementValue_3()
		throws Exception {
		ElementList fixture = new ElementList();
		fixture.elementList = new ArrayList<JAXBElement<String>>();
		String name = "";
		String value = "";

		boolean result = fixture.changeElementValue(name, value);

		// add additional test code here
		assertEquals(false, result);
	}

	/**
	 * Run the Map<String, String> getElementMap() method test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@Test
	public void testGetElementMap_1()
		throws Exception {
		ElementList fixture = new ElementList();
		fixture.elementList = new ArrayList<JAXBElement<String>>();

		Map<String, String> result = fixture.getElementMap();

		// add additional test code here
		assertEquals(null, result);
	}

	/**
	 * Run the Map<String, String> getElementMap() method test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@Test
	public void testGetElementMap_2()
		throws Exception {
		ElementList fixture = new ElementList();
		fixture.elementList = new ArrayList<JAXBElement<String>>();

		Map<String, String> result = fixture.getElementMap();

		// add additional test code here
		assertEquals(null, result);
	}

	/**
	 * Run the Map<String, String> getElementMap() method test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@Test
	public void testGetElementMap_3()
		throws Exception {
		ElementList fixture = new ElementList();
		fixture.elementList = new ArrayList<JAXBElement<String>>();

		Map<String, String> result = fixture.getElementMap();

		// add additional test code here
		assertEquals(null, result);
	}

	/**
	 * Perform pre-test initialization.
	 *
	 * @throws Exception
	 *         if the initialization fails for some reason
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@Before
	public void setUp()
		throws Exception {
		// add additional set up code here
	}

	/**
	 * Perform post-test clean-up.
	 *
	 * @throws Exception
	 *         if the clean-up fails for some reason
	 *
	 * @generatedBy CodePro at 27.01.11 10:06
	 */
	@After
	public void tearDown()
		throws Exception {
		// Add additional tear down code here
	}
}