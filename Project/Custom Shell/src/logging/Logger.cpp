///
/// Custom Shell: the JumpShell
/// Made by Andrej, Pierre and Sebastian
///

/// localhost:tcp:9500

#include <cstring>
#include <unistd.h>
#include <netinet/ip.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include "Logger.h"

Logger::Logger() {

    debug("Logger: Constructor");
    m_conn.opened = false; // not setting socketfd deliberately
    m_conn.host = nullptr;
    memset(&m_conn.host_addr, 0, sizeof(m_conn.host_addr));

    m_conn.hostname = "127.0.0.1";
    m_conn.port = 9500;

    m_user = "";
    m_system = "";

    /// create socket
    connect();

}


Logger::~Logger() {
    debug("Logger: Destructor");
    disconnect();
}


void Logger::setLogin(const String &user) {
    m_user = user;
}

void Logger::setSystem(const String &system) {
    m_system = system;
}


void Logger::connect() {

    if (inet_pton(AF_INET, m_conn.hostname.c_str(), &(m_conn.host_addr.sin_addr)) == -1) {
        except("Error at inet_pton.");
    }

    m_conn.host_addr.sin_family = AF_INET;
    m_conn.host_addr.sin_port = htons((uint16_t) m_conn.port);

    m_conn.socketfd = socket(AF_INET, SOCK_DGRAM, IPPROTO_UDP);
    if (m_conn.socketfd < 0) {
        except("UDP socket error.");
    }

    m_conn.opened = true;

}


void Logger::disconnect() {
    if (m_conn.opened) {
        close(m_conn.socketfd);
    }
}

bool Logger::filter(char character) {

    switch (character) {
        case ESC:
        case '\a':
        case '\r':
            return true;
        default:
            return false;
    }

}



void Logger::addCharToLog(char character) {

    if (filter(character)) return;
    if (character == '\n' && !m_messageContainer.empty()) {
        send(m_messageContainer);
        m_messageContainer.clear();
    }
    else {
        m_messageContainer.push_back(character);
    }

}


void Logger::send(const String& content) {

    const json container = {
        { "ndsShell_cmd", content },
        { "user", m_user },
        { "system", m_system }
    };

    const String constructed = container.dump();

    ssize_t sent_bytes;
    char buffer[BUFFER_SIZE];
    bzero(buffer, BUFFER_SIZE);
    strcpy(buffer, constructed.c_str());

    sent_bytes = ::sendto(
        m_conn.socketfd,                             // socket descriptor
        buffer,                                      // C-style buffer
        (size_t) constructed.length(),               // number of chars to send
        0,                                           // flags - 0 because of nothing special
        (const struct sockaddr *)&m_conn.host_addr,  // again, sending the recipient address
        sizeof(m_conn.host_addr)                     // size of that address structure
    );

    if (sent_bytes == -1) {
        except("Error occured while writing to socket.");
    }
    if (sent_bytes != (ssize_t) constructed.length()) {
        except("Error occured while writing to socket: buffer written just partially.");
    }

}


void Logger::except(const String& message) {
    unused(message);
    debug(message);
    throw std::exception();
}
