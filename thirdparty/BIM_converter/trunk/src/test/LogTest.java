package test;


import org.slf4j.LoggerFactory;
import org.slf4j.Logger;

import ch.qos.logback.classic.LoggerContext;
import ch.qos.logback.core.util.StatusPrinter;

public class LogTest {
	public static void main(String[] args) {
		Logger logger = LoggerFactory.getLogger("test.LogTest");
		logger.debug("Helllooooooooo world");
		
		LoggerContext lc = (LoggerContext) LoggerFactory.getILoggerFactory();
	    StatusPrinter.print(lc);

	}

}
