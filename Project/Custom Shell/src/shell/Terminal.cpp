///
/// Custom Shell: the JumpShell
/// Made by Andrej, Pierre and Sebastian
///

#include "Terminal.h"


Terminal::Terminal() {

    /// Setting up input parsing variables
    for (bool & value : m_tdmap) value = false;

    /// https://en.wikipedia.org/wiki/Escape_sequences_in_C
    for (auto & c : " \b\t\r\n\a") {
        m_tdmap[static_cast<int>(c)] = true;
    }

    tcgetattr(fileno(stdin), &m_terminal);
    m_restore = m_terminal;

    m_terminal.c_lflag &= (~ICANON & ~ECHO);
    m_terminal.c_cc[VTIME] = 0;
    m_terminal.c_cc[VMIN] = 1;
    enterSCMode();
    debug("Terminal mode: " + std::to_string(m_mode));

}


Terminal::~Terminal() {
    restoreTerminal();
}


/**
 * Restores custom terminal behavior modified by termios library.
 */
void Terminal::restoreTerminal() {
    if (m_mode == TMode::SINGLECHAR) {
        tcsetattr(fileno(stdin), TCSANOW, &m_restore);
    }
}


void Terminal::enterSCMode() {
    if (tcsetattr(fileno(stdin), TCSANOW, &m_terminal) < 0) {
        m_mode = TMode::STANDARD;
        m_readbuffer = nullptr;
        restoreTerminal();
    }
    else {
        m_mode = TMode::SINGLECHAR;
        m_readbuffer = std::cin.rdbuf();
    }
}


/**
 * Single Character getChar.
 * Gets valid character to be printed. If shouldn't, bool value is false.
 * @return {char, boolean}
 */
Character Terminal::sc_getChar() {

    Character c = {true, 0};
    do {
        c.value = m_readbuffer->sbumpc();
        if (!c.valid) {
            switch (c.value) {
                case ARR_UP:
                case ARR_DOWN:
                case ARR_RIGHT:
                case ARR_LEFT:
                    return c;
            }
        }

        // Intervene if ESC or ARROW prefix is entered
        switch (c.value) {
            case ESC:   c.valid = false; break;
            case ARROW: c.valid = false; break;
            default:    c.valid = true;
        }

    } while (!c.valid);

    return c;
}


/**
 * Single Character based getLine.
 * @return single string of whole line.
 */
String Terminal::sc_getLine() {

    String line;
    Character c;
    bool done = false;
    while (!done) {
        if (m_readbuffer->sgetc() == EOF)
            done = true;

        c = sc_getChar();
        if (c.value == ENTER) {
            done = true;
        }
        else if (c.value == BACKSPACE || c.value == DELETE) {
            if (line.length() > 0) {
                std::cout << c.value;
                line.pop_back();
            }
            continue;
        }

        if (c.valid) {
            std::cout << c.value;
            line.push_back(c.value);
        }
    }
    return line;

}


/**
 * Standard based getline.
 * @return single string of whole line.
 */
String Terminal::std_getLine() {

    String line;
    std::getline(std::cin, line);
    return line;

}


/**
 * Utilises getLine implementation from the chosen one.
 * @return vector of strings in the line.
 */
StringVec Terminal::getInputLine() {

    StringVec res;
    String line, token;

    line = (m_mode == SINGLECHAR) ? sc_getLine() : std_getLine();
    for (auto &c : line) {
        // iterate over each char and check for delimiter
        if (c >= 0 && m_tdmap[static_cast<int>(c)]) {
            if (!token.empty()) {
                res.push_back(token);
                token.clear();
            }
        }
        else token += c;
    }
    if (!token.empty()) {
        res.push_back(token);
    }
    return res;

}


String Terminal::joinLine(const StringVec& vector) {

    String res;
    for (auto &item : vector) {
        res.append(item);
        res.append(" ");
    }
    res.pop_back();
    return res;

}
