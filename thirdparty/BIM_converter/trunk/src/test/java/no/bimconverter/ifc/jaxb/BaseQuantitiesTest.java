package no.bimconverter.ifc.jaxb;


import java.util.ArrayList;
import java.util.List;
import javax.xml.bind.JAXBElement;
import org.junit.*;
import static org.junit.Assert.*;
public class BaseQuantitiesTest {

	/**
	 * Run the List<JAXBElement<String>> getBaseQuantities() method test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:00
	 */
	@Test
	public void testGetBaseQuantities_1()
		throws Exception {
		BaseQuantities fixture = new BaseQuantities();
		fixture.setBaseQuantities(new ArrayList<JAXBElement<String>>());

		List<JAXBElement<String>> result = fixture.getBaseQuantities();

		// add additional test code here
		assertNotNull(result);
		assertEquals(0, result.size());
	}

	/**
	 * Run the void setBaseQuantities(List<JAXBElement<String>>) method test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:00
	 */
	@Test
	public void testSetBaseQuantities_1()
		throws Exception {
		BaseQuantities fixture = new BaseQuantities();
		fixture.setBaseQuantities(new ArrayList<JAXBElement<String>>());
		List<JAXBElement<String>> units = new ArrayList<JAXBElement<String>>();

		fixture.setBaseQuantities(units);

		// add additional test code here
	}

	/**
	 * Perform pre-test initialization.
	 *
	 * @throws Exception
	 *         if the initialization fails for some reason
	 *
	 * @generatedBy CodePro at 27.01.11 10:00
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
	 * @generatedBy CodePro at 27.01.11 10:00
	 */
	@After
	public void tearDown()
		throws Exception {
		// Add additional tear down code here
	}
	
	/**
	 * Run the BaseQuantities() constructor test.
	 *
	 * @throws Exception
	 *
	 * @generatedBy CodePro at 27.01.11 10:00
	 */
	@Test
	public void testBaseQuantities_1()
		throws Exception {

		BaseQuantities result = new BaseQuantities();

		// add additional test code here
		assertNotNull(result);
		assertEquals(null, result.getElementMap());
	}

}
