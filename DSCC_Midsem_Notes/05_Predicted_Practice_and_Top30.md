# EXTRA PRACTICE QUESTIONS + TOP-30 LIST

> ## 🎯 Claude Predicted Practice Questions
> **These are predicted/practice questions based on syllabus and emphasis, not leaked or guaranteed questions.**
> They are *not* copied from your assignments. Each has a pointer (→) to the section that contains the answer. Numerical answers are given at the end.

---

# PART A — 30 SHORT QUESTIONS (1–2 marks)

*Claude Predicted Practice Question* — each one.

1. Why is "no shared memory" a defining property of a distributed system? → 01 §1
2. Give two examples of heterogeneity in a distributed system and how middleware handles it. → 01 §2
3. What is location transparency? Give an example. → 01 §2
4. Why is a server called a single point of failure? → 01 §3.1
5. What is a DHT in P2P? (one line) → 01 §3.2
6. Name two real-world grid computing projects. → 01 §3.3
7. List three characteristics of cloud computing. → 01 §3.4
8. What is horizontal scalability? → 01 §4
9. What is meant by "openness" of a distributed system? → 01 §2
10. What is jitter and where does it matter? → 01 §5.3
11. Why can't timeouts detect failures reliably in an asynchronous system? → 01 §5.3
12. Give one threat to a channel in the security model. → 01 §5.3
13. Which device joins two different networks? → 01 §6
14. Why is UDP used for video streaming? → 01 §6
15. What is a socket? → 01 §7.1
16. What is a race condition in shared memory? → 01 §7.2
17. Why can pointers not be passed in RPC? → 01 §10
18. What is an IDL? → 01 §10
19. What is the difference between `bind` and `rebind` in Java RMI? → 01 §12.7
20. What does `UnicastRemoteObject` do? → 01 §12.3
21. What is a topic-based subscription? → 01 §11
22. What is a logical clock? → 02 §5
23. What is a "concurrent event"? Give an example. → 02 §4
24. Why must the receive rule of Lamport clock use `max`? → 02 §5
25. What is UTC? → 02 §7
26. What is a stable predicate? Give an example. → 02 §10
27. What are the three types of events in a process? → 02 §3
28. Why is starvation a problem in mutual exclusion? → 02 §12
29. What is the difference between delivering and receiving a multicast message? → 02 §14
30. State the FLP impossibility result in one line. → 02 §15

---

# PART B — 20 MEDIUM QUESTIONS (3–4 marks)

*Claude Predicted Practice Question* — each one.

1. Explain the three major challenges caused by the absence of a global clock in a distributed system. → 02 §1, §7
2. "A distributed system is more than a set of networked computers." Justify with reference to transparency and coordination. → 01 §1–2
3. How is cloud computing different from grid computing? Explain with examples. → 01 §3.5
4. Explain the role of middleware in masking heterogeneity. → 01 §5.2
5. Explain caching and proxy servers as architectural elements. → 01 §5.2
6. Explain mobile code and mobile agents with advantages and security concerns. → 01 §5.2
7. Explain the failure model and the notion of masking failures. → 01 §5.3
8. Describe how a synchronous system differs from an asynchronous one in designing failure detectors. → 01 §5.3
9. Why is the request–reply protocol central to RPC and RMI? Explain marshalling and semantics. → 01 §7, §9
10. Explain the three invocation semantics and state which is used by Java RMI and why idempotent operations matter. → 01 §9
11. Why are remote exceptions necessary in RMI but not for local calls? → 01 §8
12. Explain event-based (publish–subscribe) architecture and its advantages over polling. → 01 §11
13. Distinguish between logical clocks and vector clocks using an example where Lamport clocks fail to detect concurrency. → 02 §5–6
14. Explain how skew is bounded by drift rate and how often clocks must be synchronised. → 02 §7
15. Explain Berkeley algorithm with an example of three machines. → 02 §7
16. Why is a consistent cut needed for recording a global state? Give an example of an inconsistent cut. → 02 §9
17. What is "Possibly Φ" and "Definitely Φ" in distributed debugging? → 02 §10
18. Explain why Ricart–Agrawala guarantees mutual exclusion and freedom from starvation. → 02 §12
19. What happens in the Ring election algorithm if two processes start elections at the same time? → 02 §13
20. Explain causal ordering vs total ordering in multicast with an example each. → 02 §14

---

# PART C — 15 LONG-ANSWER QUESTIONS (7–10 marks)

*Claude Predicted Practice Question* — each one.

1. Define distributed system. Discuss its characteristics, advantages and disadvantages, and the main design challenges. → 01 §1–4
2. Discuss in detail the architectural models of distributed systems and explain how middleware supports them. → 01 §5.2
3. Explain the interaction, failure and security models. Which assumptions does each make about the network? → 01 §5.3
4. Explain the TCP/IP protocol stack and how internetworking supports distributed applications. → 01 §6
5. Explain interprocess communication using sockets (UDP and TCP), marshalling and the request–reply protocol. → 01 §7
6. Explain RPC in detail: components, steps, failure semantics and issues. Compare with RMI. → 01 §10
7. Explain the complete architecture and working of Java RMI with a student-database example. → 01 §12
8. Explain events and notifications with the publish–subscribe model, roles, subscription types and applications. → 01 §11
9. Discuss physical clock synchronisation algorithms (Cristian, Berkeley, NTP) with formulas and examples. → 02 §7
10. Explain happened-before, Lamport clocks and vector clocks; work out an example with 3 processes and 8 events. → 02 §4–6
11. Explain global states, cuts, consistency and the Chandy–Lamport algorithm; prove that the recorded state is consistent. → 02 §9
12. Discuss distributed debugging: predicates, lattice of global states, possibly/definitely, and monitor process design. → 02 §10
13. Compare all mutual exclusion algorithms on messages, delay and failure tolerance. → 02 §12
14. Discuss multicast communication: reliable multicast properties, FIFO/causal/total order and implementation. → 02 §14
15. Explain consensus, Byzantine generals and interactive consistency; state bounds on number of faulty processes. → 02 §15

---

# PART D — 10 APPLICATION / CASE-STUDY QUESTIONS

*Claude Predicted Practice Question* — each one.

1. **Online ticket booking:** many users book the last seat simultaneously. Which distributed-system characteristics and which mutual-exclusion mechanism apply? → 01 §2, 02 §12
2. **YouTube-like service:** identify architectural elements (proxy/caches, multiple servers, CDN) and fundamental-model issues (latency, failures). → 01 §5
3. **Chat application:** choose message passing vs shared memory, TCP vs UDP; justify. → 01 §6–7
4. **University ERP using Java RMI:** which interfaces and objects are remote? How does the client find the service? What invocation semantics? → 01 §12
5. **Stock-price alerts to 10,000 traders:** design using publish–subscribe; discuss reliability and ordering. → 01 §11, 02 §14
6. **Distributed logging across servers:** how would you order events from servers with drifting clocks? Compare NTP with logical clocks. → 02 §5–8
7. **Bank with branches:** design a method to compute total money at a consistent instant while transfers continue. → 02 §9
8. **Cluster with a master node** that crashes every few days: choose an election algorithm and justify; discuss the effect of a flapping high-ID node. → 02 §13
9. **Replicated database with 7 replicas, 2 may be faulty:** can consensus be reached (crash vs Byzantine)? How many rounds? → 02 §15
10. **Group chat with total ordering:** ensure every member sees messages in the same order; describe the sequencer method and its weakness. → 02 §14

---

# PART E — 10 NUMERICAL / PROBLEM-BASED QUESTIONS

*Claude Predicted Practice Question* — each one. (Answers below.)

N1. P1: a, b(send→P2), c. P2: d, e(recv from P1), f(send→P3). P3: g, h(recv from P2). All clocks start at 0. Give Lamport timestamps.
N2. P2's clock reads 8; it receives a message with timestamp 8. Then P2 sends a message. What timestamps result?
N3. Vector clocks (3 processes): P1 sends message with V = (2,0,0). P2 currently has V = (0,3,1). What is P2's clock after receiving? Does the send happen-before an event of P2 with V = (0,3,1)?
N4. Cristian: T0 = 09:15:00.000, T1 = 09:15:00.400, server time = 09:15:07.100, minimum one-way delay = 0.05 s. Find the corrected time and accuracy.
N5. Berkeley: master 10:00, slave A 09:58, slave B 10:07, slave C 10:03 (master included). Compute adjustments.
N6. Two clocks each drift at most 5×10⁻⁶ s/s. After 10 minutes since sync, what is the maximum skew?
N7. Compare number of messages for 10 processes: central server, Ricart–Agrawala, Lamport ME, ring (best/worst).
N8. Bully algorithm: 8 processes, coordinator P8 crashes, process P7 detects. How many messages? What if P1 detects (count roughly)?
N9. Ring election with N = 10, initiator is the anticlockwise neighbour of the highest-id process. Total messages? Best case?
N10. Chandy–Lamport: balances P1 = 400, P2 = 350, P3 = 250. Before snapshot: P1 sends 30 to P2. P1 starts snapshot after sending. P3 sends 10 to P1 before receiving marker. FIFO channels. Find recorded local states and channel states, verify total.

### Answers to numericals
**N1.** a=1, b=2 (msg ts 2); P2: d=1, e=max(1,2)+1=3, f=4 (ts 4); P3: g=1, h=max(1,4)+1=5. c (P1) = 3.
**N2.** Receive: max(8,8)+1 = **9**; send next: **10**.
**N3.** Receive: P2 first takes max((0,3,1),(2,0,0)) = (2,3,1), then own increment → **(2,4,1)**. Send event with (2,0,0) < (0,3,1)? Compare: 2 > 0 in first component ⇒ not ≤ ⇒ **not comparable ⇒ concurrent**, so no happened-before.
**N4.** RTT = 0.4 s → time = 7.100 + 0.2 = **09:15:07.300**. Accuracy = ±(0.2 − 0.05) = **±0.15 s**.
**N5.** Offsets: master 0, A −2, B +7, C +3. Avg = (0 −2 +7 +3)/4 = **+2 min** → target 10:02. Adjustments: master +2; A +4; B −5; C −1.
**N6.** Max skew = 2ρΔt = 2 × 5×10⁻⁶ × 600 = **6 ms**.
**N7.** N = 10: central = 3; RA = 2×9 = **18**; Lamport = 3×9 = **27**; ring = 1 to 9 messages per entry (plus continuous token circulation).
**N8.** P7 detects: ELECTION to P8 (1); P8 silent; P7 wins after timeout; COORDINATOR to P1–P6 = 6 ⇒ **N−2 = 6 coordinator messages + 1 election = 7**. If P1 detects: ELECTION to P2–P8 (7), OK replies from P2–P7 (6), and each of P2–P7 starts its own election to higher ids… total is on the order of **N² ≈ 60+** (roughly 7+6+ (6+5+…+1 elections + replies) + 7 coordinator).
**N9.** Worst case = 3N−1 = **29** messages (9 election to reach highest + 10 to circle back + 10 elected). Best case = 2N = **20**.
**N10.** Sequence: P1 sends 30 to P2 (P1 = 370, m in C12). P1 records **S1 = 370**, sends markers on C12, C13, records C21, C31. P3 sends 10 to P1 (P3 = 240, message on C31 before its marker). P2 receives 30 (P2 = 380) **then** marker on C12 → records **S2 = 380**, C12 = {}. P3 gets marker → records **S3 = 240**, C13 = {}. P1 receives 10 from P3 while recording → **C31 = {10}**, then marker. Total = 370 + 380 + 240 + 10 = **1000** = 400 + 350 + 250 ✓.

---

# 🔥 TOP 30 QUESTIONS TO PREPARE FIRST

*(Ranking is my judgement from the official syllabus + the emphasis in your two BPIT assignments. It is not a guarantee.)*
Priority: 🔥🔥🔥 very high, 🔥🔥 high, 🔥 medium. Marks = expected marks at typical BPIT/GGSIPU-style weightage.

| # | Question | Unit | Topic | Exp. marks | Priority | Why important |
|---|---|---|---|---|---|---|
| 1 | Design a Java RMI application (calculator/student service): architecture, interface, server, client, registry, code, output | I | Java RMI | 5–10 | 🔥🔥🔥 | Assignment case study + official "Case study – Java RMI" |
| 2 | Explain distributed objects and RMI (remote object, interface, stub/proxy, steps) | I | Distributed objects / RMI | 4–8 | 🔥🔥🔥 | A1-3b; syllabus |
| 3 | RMI vs RPC | I | RMI/RPC | 3–5 | 🔥🔥🔥 | Standard comparison; both in syllabus |
| 4 | Message passing vs shared memory | I | IPC | 3–5 | 🔥🔥🔥 | A1-3a explicit comparison |
| 5 | Compare client–server, P2P, grid, cloud | I | Examples of DS | 4–5 | 🔥🔥🔥 | A1-1b + syllabus lists the four |
| 6 | Explain system models: architectural & fundamental (diagrams) | I | System models | 5–10 | 🔥🔥🔥 | A1-2a |
| 7 | Define DS and explain characteristics with examples | I | Characteristics | 2–5 | 🔥🔥🔥 | A1-1a; base for compulsory question |
| 8 | Networking & internetworking: role of protocols | I | Networking | 3–5 | 🔥🔥 | A1-2b |
| 9 | Explain RPC (stubs, steps, semantics) | I | RPC | 3–5 | 🔥🔥 | Syllabus topic; needed for RMI vs RPC |
| 10 | Events and notifications (publish–subscribe) | I | Events | 3–5 | 🔥 | Syllabus topic, not in assignment → short/medium likely |
| 11 | Advantages of distributed systems | I | Advantages | 1–3 | 🔥 | Syllabus; easy marks |
| 12 | Lamport logical clock: rules + timestamp numerical | II | Logical clocks | 3–5 | 🔥🔥🔥 | A2-1b; numerical topic |
| 13 | Happened-before relation with example | II | Events/order | 2–3 | 🔥🔥🔥 | A2-1b; foundation |
| 14 | Vector clocks vs Lamport | II | Logical time | 3–4 | 🔥 | Natural follow-up; tough-paper question |
| 15 | Need for physical clock sync; skew, drift, delay | II | Physical clocks | 3–4 | 🔥🔥 | A2-1c |
| 16 | Cristian & Berkeley algorithms (with numericals) | II | Physical clocks | 3–5 | 🔥🔥 | Standard numericals; supports A2-1c |
| 17 | Logical vs physical time table | II | Time | 2–3 | 🔥🔥 | Standard compare question |
| 18 | Events and process states; role in synchronisation | II | Sync intro | 2 | 🔥🔥 | A2-1a |
| 19 | Global state: components and importance of consistency | II | Global state | 3–4 | 🔥🔥🔥 | A2-2a |
| 20 | Distributed snapshot (Chandy–Lamport) with P1, P2, P3 | II | Global state | 4–8 | 🔥🔥🔥 | A2-2b case study |
| 21 | Consistent vs inconsistent cut (diagram) | II | Global state | 2–3 | 🔥🔥 | Ties to 19–20 |
| 22 | Distributed debugging | II | Debugging | 2–4 | 🔥 | Syllabus; not in assignment |
| 23 | Requirements of mutual exclusion + any two algorithms | II | Mutual exclusion | 4–8 | 🔥🔥🔥 | A2-3a |
| 24 | Ricart–Agrawala algorithm with example & message complexity | II | Mutual exclusion | 4–6 | 🔥🔥🔥 | Most standard permission-based algorithm |
| 25 | Election: Bully algorithm with example | II | Election | 4–6 | 🔥🔥🔥 | A2-3b |
| 26 | Ring election algorithm with example | II | Election | 4–6 | 🔥🔥🔥 | A2-3b |
| 27 | Bully vs Ring comparison (messages, assumptions) | II | Election | 3–5 | 🔥🔥🔥 | A2-3b asks it explicitly |
| 28 | Consensus: definition, agreement, validity, termination, example | II | Consensus | 3–5 | 🔥🔥🔥 | A2-4a |
| 29 | Consensus with 5 processes, 1 may fail; failures & delays | II | Consensus | 4–8 | 🔥🔥🔥 | A2-4b case study |
| 30 | Multicast communication: reliable, FIFO/causal/total ordering | II | Multicast | 3–5 | 🔥 | Syllabus; likely short/medium |

**Study order suggestion:** 1–7 (Unit-I core) → 12, 13, 19, 20 (Unit-II core numericals/diagram) → 23–29 (algorithms) → 15–18 → the 🔥 items.
