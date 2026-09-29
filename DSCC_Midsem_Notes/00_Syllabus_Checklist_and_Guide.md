# CIE-407T — Distributed Systems & Cloud Computing
## MID-SEM Study Pack: UNIT-I + UNIT-II ONLY
**BPIT (IT, Sem VII) · GGSIPU (USICT handbook, batch 2021-22 onwards)**

---

## 0. How to use this pack

| File | What is inside |
|---|---|
| `00_Syllabus_Checklist_and_Guide.md` | This file – checklist, source labels, exam pattern |
| `01_Unit1_Notes.md` | Complete Unit-I notes (Intro, models, IPC, RMI, RPC, events, Java RMI) |
| `02_Unit2_Notes.md` | Complete Unit-II notes (time, clocks, global state, mutual exclusion, election, multicast, consensus) |
| `03_Faculty_Emphasis_and_Quick_Tables.md` | Faculty-emphasis ranking, definitions, comparisons, diagrams, algorithms |
| `04_Question_Bank_with_Answers.md` | Question bank (questions first, model answers after) |
| `05_Predicted_Practice_and_Top30.md` | Extra "Claude Predicted Practice Questions" + Top-30 list |
| `06_Model_Papers.md` | 3 model mid-sem papers + answer key |
| `07_Revision_Viva_and_Writing.md` | One-day revision sheet, 30 viva Qs, answer-writing format |

**Suggested order:** Notes (files 01, 02) → Faculty emphasis (03) → attempt questions in 04 *without looking at answers* → 05 → model papers under time → 07 the night before.

---

## 1. Source labels used everywhere

| Label | Meaning |
|---|---|
| 📘 **[Official syllabus]** | The topic is written in the GGSIPU syllabus PDF you uploaded |
| 📝 **[Assignment-emphasized]** | The topic/question appears in your BPIT Assignment-1 or Assignment-2 |
| ➕ **[Additional / exam-oriented]** | NOT named in the syllabus, but added because textbooks (Coulouris – Distributed Systems: Concepts and Design; Ghosh; Tanenbaum) cover it under the same topic and examiners often expect it |
| 🎯 **[Claude Predicted Practice Question]** | A question I created for practice. **These are predicted/practice questions based on syllabus and emphasis, not leaked or guaranteed questions.** |

> ⚠️ Nothing here is guaranteed to appear. The notes are built to make you *understand* the topics so you can answer any variation.

---

## 2. What the syllabus PDF actually says (verbatim summary)

**Paper:** CIE-407T, Distributed Systems and Cloud Computing, L-3, Credits 3. Theory: 25 marks internal (TCE) + 75 marks term-end.

**UNIT-I – Introduction to Distributed Systems:** Characteristics of distributed systems – Introduction; Examples of distributed systems (client-server, peer-to-peer, grid and cloud computing); Advantages of distributed systems; System models – Introduction, Architectural and Fundamental models; Networking and Internetworking; Interprocess Communication (message passing and shared memory); Distributed objects and Remote Method Invocation; RPC; Events and notifications; Case study – Java RMI.

**UNIT-II – Synchronization:** Time and Global States – Introduction; Logical clocks; Synchronizing physical clocks; events and process states; logical time and logical clocks; global states; distributed debugging. Coordination and Agreement: Distributed mutual exclusion; Elections; Multicast communication; consensus and related problems.

**Textbooks (from syllabus):** (1) Coulouris, Dollimore, Kindberg – *Distributed Systems: Concepts and Design*, 4th ed., Pearson; (2) Buyya – *Cloud Computing Principles and Paradigms*; (3) S. Ghosh – *Distributed Systems*, CRC Press 2010.
**References:** Tanenbaum & van Steen; Kshemakalyani & Singhal; Gerard Tel.

**Term-end paper pattern (from the handbook) – useful to know how questions are framed:**
- 9 questions in total. Q1 compulsory, objective/short, covers the entire syllabus, **15 marks**.
- 4 units, each unit has 2 questions (attempt any 1), each question can have up to 5 sub-parts, **each unit = 15 marks**.
- Standard of questions = level of the prescribed textbook.

> Note: your **mid-sem** is an internal BPIT exam, so the exact pattern may differ. Ask your faculty for marks/duration. This pack prepares 1-mark to 10-mark answers so that any pattern is covered.

**Out of scope for mid-sem – "Outside Mid-Sem Syllabus":** Unit-III (Distributed File Systems, Hadoop, load balancing/sharing, task assignment) and Unit-IV (Cloud Computing layers, IaaS/PaaS/SaaS, migration). Only the *idea* of "cloud computing" as an **example of a distributed system** is in Unit-I, so it is covered briefly there.

---

## 3. MID-SEM SYLLABUS CHECKLIST (tick as you finish)

### UNIT-I
| # | Topic (as in your brief) | Where | Assignment link | Done |
|---|---|---|---|---|
| 1 | Introduction to Distributed Systems | 01 §1 | A1-1a | ☐ |
| 2 | Characteristics of Distributed Systems | 01 §2 | A1-1a | ☐ |
| 3a | Example: Client–Server | 01 §3.1 | A1-1b | ☐ |
| 3b | Example: Peer-to-Peer | 01 §3.2 | A1-1b | ☐ |
| 3c | Example: Grid Computing | 01 §3.3 | A1-1b | ☐ |
| 3d | Example: Cloud Computing (as an example only) | 01 §3.4 | A1-1b | ☐ |
| 3e | Comparison CS vs P2P vs Grid vs Cloud | 01 §3.5 | A1-1b | ☐ |
| 4 | Advantages of Distributed Systems | 01 §4 | – | ☐ |
| 5a | System Models – Introduction | 01 §5.1 | A1-2a | ☐ |
| 5b | Architectural Models | 01 §5.2 | A1-2a | ☐ |
| 5c | Fundamental Models (interaction, failure, security) | 01 §5.3 | A1-2a | ☐ |
| 6 | Networking and Internetworking | 01 §6 | A1-2b | ☐ |
| 7a | IPC – Message Passing | 01 §7.1 | A1-3a | ☐ |
| 7b | IPC – Shared Memory | 01 §7.2 | A1-3a | ☐ |
| 7c | Message Passing vs Shared Memory | 01 §7.3 | A1-3a | ☐ |
| 8 | Distributed Objects | 01 §8 | A1-3b | ☐ |
| 9 | Remote Method Invocation (RMI) | 01 §9 | A1-3b, A1-1c | ☐ |
| 10 | RPC | 01 §10 | (indirect) | ☐ |
| 10b | RMI vs RPC | 01 §10.5 | – | ☐ |
| 11 | Events and Notifications | 01 §11 | – | ☐ |
| 12 | Case Study: Java RMI | 01 §12 | A1-1c | ☐ |

### UNIT-II
| # | Topic | Where | Assignment link | Done |
|---|---|---|---|---|
| 1 | Introduction to Synchronization | 02 §1 | A2-1a | ☐ |
| 2 | Time and Global States (intro) | 02 §2 | A2-1a | ☐ |
| 3 | Logical Clocks | 02 §5 | A2-1b | ☐ |
| 4 | Synchronizing Physical Clocks (Cristian, Berkeley, NTP) | 02 §7 | A2-1c | ☐ |
| 5 | Events and Process States | 02 §3 | A2-1a | ☐ |
| 6 | Logical Time and Logical Clocks (Lamport, Vector) | 02 §4–6 | A2-1b | ☐ |
| 6b | Logical vs Physical time | 02 §8 | – | ☐ |
| 7 | Global States (cut, snapshot) | 02 §9 | A2-2a, 2b | ☐ |
| 8 | Distributed Debugging | 02 §10 | – | ☐ |
| 9 | Coordination and Agreement (intro) | 02 §11 | – | ☐ |
| 10 | Distributed Mutual Exclusion | 02 §12 | A2-3a | ☐ |
| 11 | Election Algorithms (Bully, Ring) | 02 §13 | A2-3b | ☐ |
| 12 | Multicast Communication | 02 §14 | – | ☐ |
| 13 | Consensus and Related Problems | 02 §15 | A2-4a, 4b | ☐ |

**Coverage statement:** every one of the 21 Unit-I and 13 Unit-II items in your brief has a section in the notes.

---

## 4. Symbols used in diagrams
```
P1, P2, P3   = processes            ──►  message / arrow
[ ]          = a box (component)    ─ ─►  reply / return
●            = event on timeline    ✗    crashed process
```
Hinglish tips are marked with 🗣️.
